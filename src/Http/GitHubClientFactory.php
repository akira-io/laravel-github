<?php

declare(strict_types=1);

namespace Akira\GitHub\Http;

use Github\Client;
use Github\HttpClient\Plugin\GithubExceptionThrower;
use Http\Client\Common\Plugin;
use Http\Client\Common\PluginClient;
use Psr\Http\Client\ClientInterface;

/**
 * Factory responsible for creating and configuring a GitHub Client instance.
 */
final class GitHubClientFactory
{
    /**
     * Build a configured GitHub client.
     *
     * @param  string|null  $enterpriseUrl  GitHub Enterprise base URL
     * @param  string|null  $token  Personal access token
     * @param  bool  $debug  Enable debug plugins
     */
    public function make(?string $enterpriseUrl = null, ?string $token = null, bool $debug = false): Client
    {
        $client = new Client();
        if ($enterpriseUrl) {
            $client->setEnterpriseUrl($enterpriseUrl);
        }
        if ($token) {
            $client->authenticate($token, null, Client::AUTH_ACCESS_TOKEN);
        }

        if ($debug) {
            $httpClient = $this->decorateWithPlugins($client->getHttpClient(), [
                new GithubExceptionThrower(),
            ]);
            $client->setHttpClient($httpClient);
        }

        return $client;
    }

    private function decorateWithPlugins(ClientInterface $httpClient, array $plugins): ClientInterface
    {
        return new PluginClient($httpClient, array_filter($plugins, fn ($p) => $p instanceof Plugin));
    }
}
