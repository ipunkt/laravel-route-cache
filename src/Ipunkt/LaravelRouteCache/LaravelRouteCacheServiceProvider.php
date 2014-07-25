<?php namespace Ipunkt\LaravelRouteCache;

use Illuminate\Support\ServiceProvider;

class LaravelRouteCacheServiceProvider extends ServiceProvider
{

    /**
     * Sadly Route-Filter don't trigger registration of service provider
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * register Route Filter, Event Listener and internal RouteCache-Class
     *
     * @return void
     */
    public function register()
    {
        /**
         * @var \Route $router
         */
        $router = $this->app->make('router');
        $router->filter('cache.before', 'Ipunkt\LaravelRouteCache\RouteCachingFilter@before');
        $router->filter('cache.after', 'Ipunkt\LaravelRouteCache\RouteCachingFilter@after');

        /**
         * Register RouteCache to IoC
         */
        $this->app['routecache'] = $this->app->share(function ($app) {
            return new RouteCache;
        });

        /**
         * register the handler for entitymodified events
         */
        \Event::listen('entity.modified', 'Ipunkt\LaravelRouteCache\EntityModifiedHandler');
    }

    public function provides()
    {
        return [
            'cache.before',
            'cache.after',
            'routecache',
            'entity.modified',
        ];
    }

}
