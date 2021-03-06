# Route Cache for Laravel 4.x
[![Latest Stable Version](https://poser.pugx.org/ipunkt/laravel-route-cache/v/stable.svg)](https://packagist.org/packages/ipunkt/laravel-route-cache) [![Latest Unstable Version](https://poser.pugx.org/ipunkt/laravel-route-cache/v/unstable.svg)](https://packagist.org/packages/ipunkt/laravel-route-cache) [![License](https://poser.pugx.org/ipunkt/laravel-route-cache/license.svg)](https://packagist.org/packages/ipunkt/laravel-route-cache) [![Total Downloads](https://poser.pugx.org/ipunkt/laravel-route-cache/downloads.svg)](https://packagist.org/packages/ipunkt/laravel-route-cache)


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

Add `'before' => 'cache.before'` to every route you like to cache. **The route has to be canonical!**, because the filter caches just by extracting the `url()` from the Request. (maybe i change that to `fullUrl()`)

To outdate a cache, just fire the Event `entity.modified` and attach the Request or URL of the modified ressource. You can force that on your browser by attaching the GET-Parameter set in your config as `cachebuster` (default is "renew-cache") to the URL.

If the `infoheader` config setting is not `false` the library adds a Header to the Response to show wether the content came from cache or the controller.

## Other usages

The class `Ipunkt\LaravelRouteCache\RouteCache` works out of the box without the filter. You can use it in your controllers

	// get RouteCache-Instance
	$routecache = \App::make('routecache');
	$routecache->setEntityFromRequest($request);
	// or
	$routecache->setEntityFromUrl($url);
	
	// to remove the cache
	$routecache->removeCache()
	
	// to check if the client has a valid (same ETag) Cache
	$routecache->checkClientHasValidCache()
	
	// to save a Response to the Cache
	$routecache->setCacheFromResponse(Response $response)
	
	// to save string-content to the Cache
	$routecache->setCacheFromContent($content)
	
	// to get a saved Response from the Cache
	$routecache->getResponseFromCache()
	
	// to get a saved string from the Cache
	$routecache->getContentFromCache()
	
	// you can get the ETag for the entity (but you don't need it)
	$routecache->getETag()
	
	// you can even set a custom ETag if you like
	$routecache->getETag($value)

## Open TODOs
Feel free to fork and push changes
* remember all cached entities and allow to clear them with a Command
* add TestCases
* cache content-type of the content and respond with that.
* Routines to handle POST, PUT, UPDATE and DELETE Requests
