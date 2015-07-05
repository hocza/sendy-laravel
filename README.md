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
Add the following settings to the config/app.php

Service provider:

```php
'providers' => [
	...
	'Hocza\Sendy\SendyServiceProvider',
]
```

For the `Sendy::` facade

```php
'aliases' => [
	...
	'Sendy' => 'Hocza\Sendy\Facades\Sendy',
]
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
###Subscribe:

```php
$data = [
	'email' => 'johndoe@example.com',
	'name' => 'John Doe',
	'any_custom_column' => 'value'
];
Sendy::subscribe($data);
```

**RESPONSE** *(array)*

In case of success:

```php
['status' => true, 'message' => 'Subscribed']
['status' => true, 'message' => 'Already subscribed']
```
In case of error:

```php
['status' => false, 'message' => 'The error message']
```

###Unsubscribe:

```php
$email = 'johndoe@example.com';
Sendy::unsubscribe($email);
```

**RESPONSE** *(array)*

In case of success:

```php
['status' => true, 'message' => 'Unsubscribed']
```
In case of error:

```php
['status' => false, 'message' => 'The error message']
```

###Subscription status

```php
$email = 'johndoe@example.com';
Sendy::status($email);
```

**RESPONSE** *(Plain text)*

Success: `Subscribed`

Success: `Unsubscribed`

Success: `Unconfirmed`

Success: `Bounced`

Success: `Soft bounced`

Success: `Complained`

Error: `No data passed`

Error: `API key not passed`

Error: `Invalid API key`

Error: `Email not passed`

Error: `List ID not passed`

Error: `Email does not exist in list`

###Active subscriber count

```php
Sendy::count();
#To check other list:
Sendy::setListId($list_id)->count();
```

**RESPONSE** *(Plain text)*

Success: `You'll get an integer of the active subscriber count`

Error: `No data passed`

Error: `API key not passed`

Error: `Invalid API key`

Error: `List ID not passed`

Error: `List does not exist`


###Create campaign

```php
Sendy::createCampaign($campaignOptions, $campaignContent);
```

###Change list ID

To change the default list ID simply prepend with setListId($list_id)  
**Examples:**  
`Sendy::setListId($list_id)->subscribe($data);`  
`Sendy::setListId($list_id)->unsubscribe($email);`  
`Sendy::setListId($list_id)->status($email);`  
`Sendy::setListId($list_id)->count();`

Todo
---

* Implementing the rest of the API. :)
* better documentation - in progress as you can see :)
