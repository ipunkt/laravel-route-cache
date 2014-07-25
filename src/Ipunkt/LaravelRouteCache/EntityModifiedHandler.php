<?php
/**
 * Created by PhpStorm.
 * User: bastian
 * Date: 24.07.14
 * Time: 11:30
 */

namespace Ipunkt\LaravelRouteCache;


use Illuminate\Http\Request;

class EntityModifiedHandler
{
    /**
     * @var RouteCache
     */
    private $routecache;

    public function __construct()
    {
        $this->routecache = \App::make('routecache');
    }

    /**
     * Removes the Contentcache for the current or given Request/URL
     * @param array|string|Request|null $data
     */
    public function handle($data)
    {
        if (is_array($data)) {
            foreach ($data as $recData) {
                $this->handle($recData);
            }
            return;
        }

        if ($data instanceof Request) {
            $this->routecache->setEntityFromRequest($data);
        }

        if (is_string($data)) {
            $this->routecache->setEntityFromUrl($data);
        }

        $this->routecache->removeCache();
    }
} 