<?php
/**
 * Created by PhpStorm.
 * User: bastian
 * Date: 24.07.14
 * Time: 11:34
 */

namespace Ipunkt\LaravelRouteCache;


use App;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class RouteCache
{
    /**
     * @var bool
     */
    protected $isFromCache = false;

    /**
     * @var \Cache $cache
     */
    private $cache;

    private $option = [];

    /**
     * @var string|null
     */
    protected $entity;

    /**
     * @var Request
     */
    protected $request;

    /**
     * Initialize RouteCache
     */
    public function __construct()
    {
        $this->cache = App::make('cache');
        $this->request = App::make('request');

        /**
         * @var \Config $config
         */
        $config = App::make('config');
        $this->option = $config->get('laravel-route-cache::config');

        $this->setEntityFromRequest($this->request);

        /**
         * If Request contains cachebuster Input-Parameter, remove the Cache
         */
        if ($this->request->input($this->option('cachebuster')) !== null) {
            $this->removeCache();
        }
    }

    /**
     * Removes Cache for given entity
     * @param null $entity
     */
    public function removeCache($entity = null)
    {
        if ($entity === null) {
            $entity = $this->entity;
        }
        $this->cache->forget('etag-' . $entity);
        $this->cache->forget('content-' . $entity);
    }

    /**
     * Check if the Request contains the same ETag as cached for the Entity
     * @return bool
     */
    public function checkClientHasValidCache()
    {
        if ($this->option('send-not-modified-status') === false) {
            return false;
        }

        $etag = $this->getETag();

        $clientHasValidCache = $this->checkRequestHasETag($etag);
        return $clientHasValidCache;
    }

    /**
     * @return Response|null
     */
    public function getResponseFromCache()
    {
        if ($this->option('cache-static-content') === false || !$this->cache->has('content-' . $this->entity)) {
            return null;
        }

        /**
         * Generate new Response from cached content
         */
        $response = new Response();
        $this->isFromCache = true;

        $response->setContent($this->cache->get('content-' . $this->entity));

        /**
         * TODO check if setting content-type is needed
         */
        $this->setResponseHeader($response);

        return $response;
    }

    /**
     * Check if the current Request has the given ETag
     *
     * @param null $etag
     * @return bool
     */
    protected function checkRequestHasETag($etag = null)
    {
        if ($etag === null) {
            return false;
        }
        $requestEtags = $this->request->getETags();
        foreach ($requestEtags as $retag) {
            $retag = str_replace('"', '', $retag);
            if ($etag === $retag) {
                return true;
            }
        }
        return false;
    }


    /**
     * Create the entity we will be searching etag for
     * @param Request $request
     * @return string
     */
    public function setEntityFromRequest(Request $request)
    {
        $this->setEntityFromUrl($request->url());
    }

    /**
     * Create the entity we will be searching etag for
     * @param $url
     * @return string
     */
    public function setEntityFromUrl($url)
    {
        $this->entity = Str::slug($url);
    }

    /**
     * Get option
     * @param $key
     * @param null $default
     * @return null
     */
    public function option($key, $default = null)
    {
        return isset($this->option[$key]) ? $this->option[$key] : $default;
    }


    /**
     * Returns ETag
     * @return string|null
     */
    public function getETag()
    {
        return $this->cache->get('etag-' . $this->entity);
    }

    /**
     * Set ETag to Cache
     * @param string $value
     */
    public function setETag($value)
    {
        /**
         * 0 as minutes stores forever
         */
        $this->cache->put('etag-' . $this->entity, $value, 0);
    }


    /**
     * Generate and return ETag from Response
     * @param Response $content
     * @return string
     */
    public function setETagFromResponse(Response $response)
    {
        return $this->setETagFromContent($response->getContent());
    }

    /**
     * Generate and return ETag from Content
     * @param string $content
     * @return string
     */
    public function setETagFromContent($content)
    {
        $etag = md5($this->entity . $content);
        \Log::debug('Created new ETag for ' . $this->entity . ': ' . $etag);
        $this->setETag($etag);
        return $etag;
    }


    /**
     * @param Response $response
     */
    public function setCacheFromResponse(Response $response)
    {
        $this->setCacheFromContent($response->getContent());

    }

    /**
     * @param string $content
     */
    public function setCacheFromContent($content)
    {
        if ($this->option['cache-static-content'] === true) {
            /**
             * 0 as minutes stores forever
             */
            $this->cache->put('content-' . $this->entity, $content, 0);
        }
        $this->setETagFromContent($content);
    }

    /**
     * Set header containing 'cache' or 'controller' to show source of content
     * @param Response|null $response
     */
    public function setResponseHeader($response = null)
    {
        if ($response === null) {
            return;
        }

        $response->setEtag($this->getETag());

        if ($this->option('infoheader') !== false) {
            $response->header($this->option('infoheader'), ($this->isFromCache) ? 'cache' : 'controller');
        }
    }

} 