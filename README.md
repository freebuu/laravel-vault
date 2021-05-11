# PHP Laravel Vault [![Documentation Status](https://readthedocs.org/projects/php-laravel-vault/badge/?version=latest)](https://php-laravel-vault.readthedocs.io/en/latest/?badge=latest)

Get your .env from remote (HaspiCorp Vault) on deploy

> **Warning! This is very beginning alpha version without usable realise.**
> **Not recommended for using now**

## Quickstart
### Install
```shell
composer require yasdelyal/php-laravel-vault
php artisan vendor:publish --tag=config --provider="YaSdelyal\LaravelVault\LaravelVaultServiceProvider"

Copied File [/vendor/yasdelyal/php-laravel-vault/config/vault.php] To [/config/vault.php]
```
### Configure
Add patches from Vault and variables to secrets in vault.php
```php 
'vars' => [
    'patches' => [
        '/secret/database/{env}'
    ],
    'patch_variables' => [
        'env' => 'production',
    ],
  ]
```

### Override credentials
Make vault.json file with Vault options - structure MUST be same as vault.php

You can override here ALL options from vault.php
```json
{
  "connections": {
    "vault": {
      "host": "http://vault",
      "role_id": "your_secret_id",
      "secret_id": "your_secret_id"
    }
  }
}
```
### Use
```shell
cat vault.json | tr -d '\n \t'  | php artisan vault:get --stdin
```

If all OK (credentials is actual and have access to secret patches), you see merged values from all patches:
```shell
+---------+------------+
| Key     | Value      |
+---------+------------+
| secret1 | value1     |
| secret2 | value2     |
+---------+------------+
```
- For save this in .env - add option --output=currentEnv
- For save this in .env.next - add option --output=nextEnv

## Documentation
Documentation WILL BE available here: https://php-laravel-vault.readthedocs.io/

