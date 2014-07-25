<?php
/**
 * Created by PhpStorm.
 * User: bastian
 * Date: 23.07.14
 * Time: 14:20
 */

namespace Ipunkt\LaravelRouteCache;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Route;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class RouteCachingFilter
 * @see http://keltdockins.com/2014/05/23/etags-with-laravel-filters/
 * @see https://github.com/titzu/Laravel-4-Route-Cache
 * @package Ipunkt\RouteCache
 */
class RouteCachingFilter
{
    /**
     * @var bool
     */
    private $isFromCache = false;

    /**
     * @var RouteCache
     */
    private $routecache;

    public function __construct()
    {
        $this->routecache = \App::make('routecache');
    }

    /**
     * Should be run before routes are executed
     * Will abort if etag match is found
     * @param Route $route
     * @param Request $request
     * @param Response|null $response
     * @return Response|null
     * @throws HttpException
     */
    public function before(Route $route, Request $request, $response = null)
    {
        /**
         * TODO implement other Request Methods
         */
        if (!in_array($request->method(), ['GET', 'HEAD'])) {
            return null;
        }

        if ($this->routecache->checkClientHasValidCache()) {
            /**
             * TODO Check if it is better to create a new 304-response
             */
            \App::abort(304);
        }

        $response = $this->routecache->getResponseFromCache();

        if (empty($response)) {
            $route->after('cache.after');
        }

        return $response;
    }

    /**
     * Should be run after a route is executed
     * Creates a new etag for this response content
     * @param Route $route
     * @param Request $request
     * @param Response|null $response
     */
    public function after(Route $route, Request $request, $response = null)
    {
        if (!($response instanceof \Illuminate\Http\Response)) {
            return;
        }

        $this->routecache->setCacheFromResponse($response);

        $this->routecache->setResponseHeader($response);
    }
}