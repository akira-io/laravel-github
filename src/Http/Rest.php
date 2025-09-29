<?php

declare(strict_types=1);

namespace Akira\GitHub\Http;

use Akira\GitHub\Events\RequestSending;
use Akira\GitHub\Events\ResponseReceived;
use Github\Client;
use Http\Discovery\Psr17FactoryDiscovery;
use Psr\Http\Client\ClientExceptionInterface;
use RuntimeException;

/**
 * Lightweight REST helper on top of the underlying GitHub HTTP client.
 */
final readonly class Rest
{
    public function __construct(private Client $client, private bool $emitEvents = true) {}

    /**
     * Send a GET request to the GitHub REST API.
     *
     * @param  string  $path  Path starting with a slash (e.g. /repos/{owner}/{repo})
     * @param  array<string,string>  $headers  Extra headers
     * @return array<string,mixed>|array<int,mixed>
     */
    public function get(string $path, array $headers = []): array
    {
        $http = $this->client->getHttpClient();
        $factory = Psr17FactoryDiscovery::findRequestFactory();
        $url = mb_rtrim('https://api.github.com', '/').'/'.mb_ltrim($path, '/');

        $req = $factory->createRequest('GET', $url)
            ->withHeader('Accept', 'application/vnd.github+json')
            ->withHeader('User-Agent', 'akira-laravel-github/1.0');
        foreach ($headers as $name => $value) {
            $req = $req->withHeader($name, $value);
        }

        if ($this->emitEvents) {
            event(new RequestSending('GET', $url, $req->getHeaders() ? array_map(fn ($v) => implode(',', $v), $req->getHeaders()) : [], null));
        }

        try {
            $resp = $http->sendRequest($req);
        } catch (ClientExceptionInterface $e) {
            throw new RuntimeException('GitHub REST call failed: '.$e->getMessage(), 0, $e);
        }

        $status = $resp->getStatusCode();
        $body = (string) $resp->getBody();

        if ($this->emitEvents) {
            event(new ResponseReceived($status, $url, $body));
        }

        $data = json_decode($body, true);

        return is_array($data) ? $data : [];
    }

    /**
     * Send a POST request to the GitHub REST API.
     *
     * @param  string  $path  Path starting with a slash
     * @param  array<string,mixed>  $body  JSON body
     * @param  array<string,string>  $headers  Extra headers
     * @return array<string,mixed>|array<int,mixed>
     */
    public function post(string $path, array $body = [], array $headers = []): array
    {
        $http = $this->client->getHttpClient();
        $factory = Psr17FactoryDiscovery::findRequestFactory();
        $streamFactory = Psr17FactoryDiscovery::findStreamFactory();

        $url = mb_rtrim('https://api.github.com', '/').'/'.mb_ltrim($path, '/');
        $payload = json_encode($body, JSON_THROW_ON_ERROR);

        $req = $factory->createRequest('POST', $url)
            ->withHeader('Accept', 'application/vnd.github+json')
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('User-Agent', 'akira-laravel-github/1.0')
            ->withBody($streamFactory->createStream($payload));

        foreach ($headers as $name => $value) {
            $req = $req->withHeader($name, $value);
        }

        if ($this->emitEvents) {
            event(new RequestSending('POST', $url, $req->getHeaders() ? array_map(fn ($v) => implode(',', $v), $req->getHeaders()) : [], $payload));
        }

        $resp = $http->sendRequest($req);
        $status = $resp->getStatusCode();
        $bodyStr = (string) $resp->getBody();

        if ($this->emitEvents) {
            event(new ResponseReceived($status, $url, $bodyStr));
        }

        $data = json_decode($bodyStr, true);

        return is_array($data) ? $data : [];
    }

    /**
     * Download a binary resource (e.g., artifacts ZIP) from the REST API.
     *
     * @param  string  $path  Path starting with a slash
     * @param  string  $dest  Absolute destination path
     * @return string The destination path
     */
    public function download(string $path, string $dest): string
    {
        $http = $this->client->getHttpClient();
        $factory = Psr17FactoryDiscovery::findRequestFactory();
        $url = mb_rtrim('https://api.github.com', '/').'/'.mb_ltrim($path, '/');

        $req = $factory->createRequest('GET', $url)
            ->withHeader('Accept', 'application/octet-stream')
            ->withHeader('User-Agent', 'akira-laravel-github/1.0');

        if ($this->emitEvents) {
            event(new RequestSending('GET', $url, $req->getHeaders() ? array_map(fn ($v) => implode(',', $v), $req->getHeaders()) : [], null));
        }

        $resp = $http->sendRequest($req);
        $content = (string) $resp->getBody();
        @mkdir(dirname($dest), 0777, true);
        file_put_contents($dest, $content);

        if ($this->emitEvents) {
            event(new ResponseReceived($resp->getStatusCode(), $url, 'binary'));
        }

        return $dest;
    }
}
