# Route Cache for Laravel 4.x

This package simplifies caching of static pages in your laravel project. It sends 304 status headers and (if available) static html without running the controller. The configuration ist easy and straight forward using a single route-filter.

## Installation

Add to your composer.json following lines

	"require": {
		"ipunkt/laravel-route-cache": "~1.0.0"
	}

Run `php artisan config:publish ipunkt/laravel-route-cache`

Add `'Ipunkt\LaravelRouteCache\LaravelRouteCacheServiceProvider',` to `providers` in `app/config/app.php`.

## Configuration

Edit `config.php` in `app/config/packages/ipunkt/laravel-route-cache` to your needs.

## How to use as Laravel Route-Filter

Add `'before' => 'cache.before'` to every route you like to cache.

To outdate a cache, just fire the Event `entity.modified` and attach the Request or URL of the modified ressource. You can force that on your browser by attaching the GET-Parameter set in your config as `cachebuster` (default is "renew-cache") to the URL.

If the `infoheader` config setting is not `false` the library adds a Header to the Response to show wether the content came from cache or the controller.

## Other usages

The class `Ipunkt\LaravelRouteCache\RouteCache` works out of the box without the filter. You can use it in your controllers

  $routecache = \App::make('routecache');

## Open TODOs
Feel free to fork and push changes
* remember all cached entities and allow to clear them with a Command
* add TestCases
* cache content-type of the content and respond with that.
* Routines to handle POST, PUT, UPDATE and DELETE Requests
