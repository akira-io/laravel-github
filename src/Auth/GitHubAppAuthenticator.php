<?php

declare(strict_types=1);

namespace Akira\GitHub\Auth;

use Firebase\JWT\JWT;
use Github\Client;
use Http\Discovery\Psr17FactoryDiscovery;
use Psr\Http\Client\ClientExceptionInterface;
use RuntimeException;

/**
 * Authenticate a GitHub Client as a GitHub App installation.
 */
final readonly class GitHubAppAuthenticator
{
    public function __construct(private Client $client) {}

    /**
     * Authenticate the client using GitHub App credentials.
     */
    public function authenticateInstallation(string $appId, string $installationId, string $privateKeyPem): void
    {
        $now = time();
        $payload = ['iat' => $now - 60, 'exp' => $now + (9 * 60), 'iss' => $appId];
        $jwt = JWT::encode($payload, $privateKeyPem, 'RS256');

        $http = $this->client->getHttpClient();
        $requestFactory = Psr17FactoryDiscovery::findRequestFactory();
        $streamFactory = Psr17FactoryDiscovery::findStreamFactory();

        $url = sprintf('https://api.github.com/app/installations/%s/access_tokens', $installationId);
        $request = $requestFactory->createRequest('POST', $url)
            ->withHeader('Authorization', 'Bearer '.$jwt)
            ->withHeader('Accept', 'application/vnd.github+json')
            ->withHeader('User-Agent', 'akira-laravel-github/1.0')
            ->withBody($streamFactory->createStream('{}'));

        try {
            $response = $http->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            throw new RuntimeException('Failed to request installation token: '.$e->getMessage(), 0, $e);
        }

        $data = json_decode((string) $response->getBody(), true);
        if (! is_array($data) || ! isset($data['token'])) {
            throw new RuntimeException('Invalid response when requesting installation token.');
        }

        $this->client->authenticate($data['token'], null, Client::AUTH_ACCESS_TOKEN);
    }
}
