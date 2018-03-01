# lumen-contentful-sync

[![Build Status](https://travis-ci.org/digiaonline/lumen-contentful-sync.svg?branch=master)](https://travis-ci.org/digiaonline/lumen-contentful-sync)
[![Coverage Status](https://coveralls.io/repos/github/digiaonline/lumen-contentful-sync/badge.svg?branch=master)](https://coveralls.io/github/digiaonline/lumen-contentful-sync?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/digiaonline/lumen-contentful-sync/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/digiaonline/lumen-contentful-sync/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/digiaonline/lumen-contentful-sync/v/stable)](https://packagist.org/packages/digiaonline/lumen-contentful-sync)
[![Total Downloads](https://poser.pugx.org/digiaonline/lumen-contentful-sync/downloads)](https://packagist.org/packages/digiaonline/lumen-contentful-sync)
[![License](https://poser.pugx.org/digiaonline/lumen-contentful-sync/license)](https://packagist.org/packages/digiaonline/lumen-contentful-sync)

## Introduction

This library provides a powerful abstraction on top of 
[digiaonline/lumen-contentful](https://github.com/digiaonline/lumen-contentful) with the goal of making it easier to 
synchronize content from Contentful to your application.

### Features

* console commands for synchronizing entries and assets
* a controller for handling webhooks
* a middleware for transparently authenticating webhook requests
* a middleware for proper New Relic transaction name instrumentation for webhook requests
* asynchronous processing support by using jobs

## Installation

1. Start by adding the library as a dependency to your application:

```bash
composer require digiaonline/lumen-contentful-sync
```

2. Copy `config/contentfulSync.php` to your configuration directory. There is only one mandatory configuration key - 
`content_types`. This array should contain a list of all the content model IDs you have in your Contentful space, e.g.:

```php
<?php

return [
    'content_types' => [
        'article',
        'person',
        'video',
    ],
    // ...
];
```

3. Extend `Digia\Lumen\ContentfulSync\Services\AbstractContentfulSyncService` and implement the necessary methods. This 
is where the logic for how to handle entries and assets will live. See the next section for more details.

4. Extend `Digia\Lumen\ContentfulSync\Providers\AbstractContentfulSyncServiceProvider` and implement the 
`registerContentfulSyncServiceBindings` method. A typical implementation would look like this:

```php
protected function registerContentfulSyncServiceBindings(Application $app)
{
    // ContentfulSyncService is the concrete implementation we made in step 3 
    $app->singleton(ContentfulSyncServiceContract::class, function (Application $app) {
        return new ContentfulSyncService($app->make(Queue::class));
    });
}
```

5. Register the service provider you just implemented:

```php
$app->register(\Your\ServiceProvider::class);
```

6. Register the console commands in your kernel:

```php
protected $commands = [
    // ...
    SyncAssetsCommand::class,
    SyncContentsCommand::class,
];
```

7. If you intend to use webhooks you will have to configure a route to the controller. In this example we will use 
both the New Relic and the webhook authentication middlewares, but both are optional:

```php
// The route URL is arbitrary, just make sure it matches what you have configured in Contentful
$app->post('/contentful/handleIncomingWebhook', [
    'middleware' => [
        \Digia\Lumen\ContentfulSync\Http\Middleware\WebhookAuthenticationMiddleware::class,
        \Digia\Lumen\ContentfulSync\Http\Middleware\NewRelicMiddleware::class,
    ],
    'uses'       => 'Digia\Lumen\ContentfulSync\Http\Controllers\ContentfulSyncController@handleIncomingWebhook',
]);
```

8. If you use the webhook authentication middleware you can configure the username and password to expect by adding 
these to your `.env` file:

```
CONTENTFUL_SYNC_WEBHOOK_USERNAME=username
CONTENTFUL_SYNC_WEBHOOK_PASSWORD=password
```

If you need more complicated logic you will have to create your own middleware.

## Implementing the service

Since all applications are different it is up to you to define how to handle your entries and assets.

The unimplemented methods give you the asset/entry in question as JSON. You will most likely want to use the SDK to 
convert these blobs to actual objects:

```php
/**
 * @inheritdoc
 */
public function handleEntryPublished(string $contentType, string $entryJson, bool $ignoreExisting): void
{
    // We're assuming here that you have injected an instance of ContentfulServiceContract
    $entry = $this->contentfulService->getClient()->reviveJson($entryJson);
    
    // You can now do e.g. $entry->getTitle(); etc. depending on your content model
}
```

## Usage

### Console commands

To synchronize all assets and all configured content types, run the following two commands:

```bash
php artisan contentful:assets:sync
php artisan contentful:contents:sync
```

To synchronize all entries for a specific content type (`article` in this case), run:

```bash
php artisan contentful:contents:sync article
```

To synchronize new entries only, run:

```bash
php artisan contentful:contents:sync article --ignoreExisting
```

You can add `-v` or `-vv` or `-vvv` to both commands to get more detailed output and a progress bar, e.g.:

```bash
$ php artisan contentful:contents:sync article --ignoreExisting -vvv
Synchronizing content of type "article"...
 300/300 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100% 3 secs/3 secs 32.0 MiB

Done, synchronized 300 entries
```

### New Relic middleware

If you use New Relic to monitor your application you will probably notice that all Contentful webhooks are lumped 
together as a single transacation (since they all use the same URL/route).

However, if you apply the 
`Digia\Lumen\ContentfulSync\Http\Middleware\NewRelicMiddleware` middleware to your route, transactions will be named 
`topic@contentType`, e.g. `ContentManagement.Entry.publish@article`. This allows you to single in on particularly slow 
webhooks.

### Asynchronous processing

In your service provider implementation you can specify an instance of `Illuminate\Contracts\Queue\Queue` to inject 
into the service. Unless you've configured a different queue specifically your application will be using the 
`SyncQueue` implementation to simulate a completely synchronous queue.

By specifying a different queue instance you can offload all the work the service does to queue workers.

## License

MIT
