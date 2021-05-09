# PHP Laravel Vault
A simple library for getting app config from remote (e.g. from HashiCorp Vault) while deploy.

## WARNING
it's very beginning alpha version. It's not tested on real projects and not recommended for use.

## Using
```shell
php artisan vault:get connection --stdin --b64 --output=console
```
Options:
- connection - Specify the Vault connection (config.vault.connections). If not present - default connection will be used
- --stdin - When present, command will be wait JSON config from stdin
- --b64 - Work only with --stdin - when present, JSON config must be base64 encoded
- --output -  Where the vars will be output. Possible: console, nextEnv, currentEnv. If not present - console will be used
    - console - print table in console
    - nextEnv - save variables in .env.next file near with current .env
    - currentEnv - backup current .env to .env.backup and save variables in .env
    
## How to use in CI

### Right way

- Make JSON configs with same structure as vault.php for all app envs (for develop, stage, prod, etc)
    - Set the host, role, secret, maybe patch vars - all same as current config
- Encode it with base64
- Set as secret on your CI (e.g. Gitlab CI Variables)
- When app is deployed, run
```shell
php artisan vault:get --stdin --b64 --output=currentEnv <<< ${BASE64_ENCODED_CONFIG}
```
- ${BASE64_ENCODED_CONFIG} - variable name in CI Pipeline
- With that, configs from CI and Laravel will be merged

### Way two - not recommended

- Put the variables from vault.php on the server vars (e.g. export VAULT_HOST='127.0.0.1)
- On deploy, run
```shell
php artisan vault:get
```

