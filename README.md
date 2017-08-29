![Sendy](http://demo.hocza.com/github/sendy-laravel/sendy-laravel.png)
# Sendy Laravel
A service provider for Sendy API in Laravel 5

<a href="https://codeclimate.com/github/hocza/sendy-laravel"><img src="https://codeclimate.com/github/hocza/sendy-laravel/badges/gpa.svg" /></a> [![Latest Stable Version](https://poser.pugx.org/hocza/sendy/v/stable)](https://packagist.org/packages/hocza/sendy) [![Total Downloads](https://poser.pugx.org/hocza/sendy/downloads)](https://packagist.org/packages/hocza/sendy) [![Latest Unstable Version](https://poser.pugx.org/hocza/sendy/v/unstable)](https://packagist.org/packages/hocza/sendy) [![License](https://poser.pugx.org/hocza/sendy/license)](https://packagist.org/packages/hocza/sendy)

## Installation
```shell
composer require hocza/sendy:1.*
```

or append your composer.json with:

```json
{
    "require" : {
        "hocza/sendy": "1.*"
    }
}
```
Add the following settings to the config/app.php

Service provider:

```php
'providers' => [
    // ...
    'Hocza\Sendy\SendyServiceProvider',
]
```

For the `Sendy::` facade

```php
'aliases' => [
    // ...
    'Sendy' => 'Hocza\Sendy\Facades\Sendy',
]
```

## Configuration
```shell
php artisan vendor:publish --provider="Hocza\Sendy\SendyServiceProvider"
```

It will create sendy.php within the config directory.

```php
<?php

return [

    'listId' => '',
    'installationUrl' => '',
    'apiKey' => '',

];
```

## Usage
### Subscribe:

```php
$data = [
    'email' => 'johndoe@example.com',
    'name' => 'John Doe',
    'any_custom_column' => 'value',
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

### Unsubscribe:

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

### Subscription status

```php
$email = 'johndoe@example.com';
Sendy::status($email);
```

**RESPONSE** *(Plain text)*

Success: 

- `Subscribed`
- `Unsubscribed`
- `Unconfirmed`
- `Bounced`
- `Soft bounced`
- `Complained`

Error:

- `No data passed`
- `API key not passed`
- `Invalid API key`
- `Email not passed`
- `List ID not passed`
- `Email does not exist in list`

### Active subscriber count

```php
Sendy::count();
#To check other list:
Sendy::setListId($list_id)->count();
```

**RESPONSE** *(Plain text)*

Success: 

- `You'll get an integer of the active subscriber count`

Error: 

- `No data passed`
- `API key not passed`
- `Invalid API key`
- `List ID not passed`
- `List does not exist`


### Create campaign

```php
<?php

$campaignOptions = [
    'from_name' => 'My Name',
    'from_email' => 'test@mail.com',
    'reply_to' => 'test@mail.com',
    'title' => 'My Campaign',
    'subject' => 'My Subject',
    'list_ids' => '1,2,3', // comma-separated, optional
    'brand_id' => 1,
    'query_string' => 'utm_source=sendy&utm_medium=email&utm_content=email%20newsletter&utm_campaign=email%20newsletter',
];
$campaignContent = [
    'plain_text' => 'My Campaign',
    'html_text' => View::make('mail.my-campaign'),
];
$send = false;

Sendy::createCampaign($campaignOptions, $campaignContent, $send);
```

**RESPONSE** *(Plain text)*

Success: 

- `Campaign created`
- `Campaign created and now sending`

Error: 

- `No data passed`
- `API key not passed`
- `Invalid API key`
- `From name not passed`
- `From email not passed`
- `Reply to email not passed`
- `Subject not passed`
- `HTML not passed`
- `List ID(s) not passed`
- `One or more list IDs are invalid`
- `List IDs does not belong to a single brand`
- `Brand ID not passed`
- `Unable to create campaign`
- `Unable to create and send campaign`

### Change list ID

To change the default list ID simply prepend with setListId($list_id)  
**Examples:**  

```php
Sendy::setListId($list_id)->subscribe($data);
Sendy::setListId($list_id)->unsubscribe($email);
Sendy::setListId($list_id)->status($email);
Sendy::setListId($list_id)->count();
```

## Todo

* Implementing the rest of the API. :)
* better documentation - in progress as you can see :)

## Buy me a coffee :)
<a href='https://pledgie.com/campaigns/31653'><img alt='Click here to lend your support to: Sendy-laravel and make a donation at pledgie.com !' src='https://pledgie.com/campaigns/31653.png?skin_name=chrome' border='0' ></a>
