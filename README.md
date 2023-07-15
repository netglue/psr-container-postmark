# PSR-11 Container Factories for Postmark API Clients

![PHPUnit Test Suite](https://github.com/netglue/psr-container-postmark/workflows/Continuous%20Integration/badge.svg)
[![Type Coverage](https://shepherd.dev/github/netglue/psr-container-postmark/coverage.svg)](https://shepherd.dev/github/netglue/psr-container-postmark)

## Introduction

This very small library provides [PSR-11](https://www.php-fig.org/psr/psr-11/) compatible factories for creating
Postmark HTTP clients for either servers for sending mail, or the admin client for managing your account.

[Postmark](https://postmarkapp.com) is a reliable transactional email delivery service. These factories return clients
from their [official PHP library](https://github.com/wildbit/postmark-php).

## Install

```bash
composer require netglue/psr-container-postmark
```

## Usage

By default, the container will look for application configuration using the id `config` which is a generally accepted standard. If your PSR-11 container doesn't return an associative array when calling `$container->get('config')` then this lib will likely be useless to you.

Postmark-specific configuration is, by default, expected underneath the `'postmark'` key, though this _can_ be modified.

Assuming defaults, your configuration for the clients should look like this:

```php
return [
    'postmark' => [
        'server_token' => 'Your Server Token',
        'server_timeout' => 30, // The default is 30, as per Postmark's libs so this option can be omitted
        'admin_token' => 'Your Account Token', // Only required if you are using the Admin client to manage an account
    ],
];
```

You would then wire up your container so that a key of your choosing is mapped to the factory classes, perhaps:

```php
return [
    'dependencies' => [
        'factories' => [
            Postmark\PostmarkClient::class => Netglue\PsrContainer\Postmark\ClientFactory::class,
            Postmark\PostmarkAdminClient::class => Netglue\PsrContainer\Postmark\AdminClientFactory::class,
        ],
    ],   
];
```

If you run multiple servers for some reason, you can wire up the factory in the following way to use different configuration for different servers:

```php
return [
    'dependencies' => [
        'factories' => [
            'EmailServer1' => [Netglue\PsrContainer\Postmark\ClientFactory::class, 'postmark_server_1'],
            'EmailServer2' => [Netglue\PsrContainer\Postmark\ClientFactory::class, 'postmark_server_2'],
        ],
    ],   
];
```

Given the above container setup, you'd need to specify two top-level configuration arrays with a server key in each for
`'postmark_server_1'` and `'postmark_server_2'`

## Laminas Integration

Because I use Laminas _(Formerly Zend)_ components a lot, this lib will auto-wire dependencies _(if you choose to allow it)_ during composer installation thanks to [laminas-component-installer](https://docs.laminas.dev/laminas-component-installer/).

_fin._
