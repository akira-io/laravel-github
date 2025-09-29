# Usage Examples

## Users

```php
$user = GitHub::user('octocat');
echo $user->login;
```

## Repositories

```php
$repo = GitHub::repo('akira', 'hunter');
```

## Issues

```php
$issues = GitHub::issues('akira', 'hunter');
$new = GitHub::createIssue('akira', 'hunter', 'Bug', 'Description');
GitHub::commentOnIssue('akira', 'hunter', $new->number, 'Working on it!');
```

## Pull Requests

```php
$prs = GitHub::pulls('akira', 'hunter');
```

## Releases

```php
$releases = GitHub::releases('akira', 'hunter');
```

## Organizations and Teams

```php
$org = GitHub::organization('akira-io');
$repos = GitHub::orgRepos('akira-io');
$teams = GitHub::teams('akira-io');
```

## Gists

```php
$gists = GitHub::gists('octocat');
$gist = GitHub::gist('123');
```

## Actions

```php
$runs = GitHub::actionsWorkflowRuns('akira', 'hunter');
GitHub::actionsRerun('akira', 'hunter', $runs[0]->id);
GitHub::actionsCancel('akira', 'hunter', $runs[0]->id);
GitHub::actionsDownloadArtifact('akira', 'hunter', $runs[0]->id, storage_path('artifact.zip'));
```

## Checks

```php
$checks = GitHub::checksForRef('akira', 'hunter', 'main');
```

## Packages

```php
$packages = GitHub::orgPackages('akira-io', 'container');
```

## Dependabot Alerts

```php
$alerts = GitHub::dependabotAlerts('akira', 'hunter');
```

## Projects V2 (GraphQL)

```php
$projects = GitHub::projectsV2('akira');
```

## Webhook Verification

```php
$isValid = GitHub::verifyWebhookSignature($secret, $payload, $signature);
```
