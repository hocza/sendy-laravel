# sendy-laravel
A service provider for Sendy API in Laravel 5

Installation
---
```shell
composer require hocza/sendy:dev-master
```

or append your composer.json with:

```json
"require": {
		"hocza/sendy": "dev-master"
	},
```

Configuration
---
```shell
php artisan vendor:publish --provider="Hocza\Sendy\SendyServiceProvider"
```

It will create sendy.php within the config directory.

```php
<?php
return array(
    'list_id' => '',
    'installation_url' => '',
    'api_key' => ''
);
```

Usage
---
Subscribe:

```php
$data = [
	'email' => 'jozsef@hocza.com',
	'name' => 'Jozsef Hocza',
	'any_custom_column' => 'value'
];
Sendy::subscribe($data);
```

Unsubscribe:

```php
$email = 'jozsef@hocza.com'
Sendy::unsubscribe($email);
```

Todo
---

* Implementing the rest of the API. :)
* better documentation?