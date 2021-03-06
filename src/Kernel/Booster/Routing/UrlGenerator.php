<?php

namespace Zento\Kernel\Booster\Routing;

use Illuminate\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Routing\Route;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

use Session;
use Config;

class UrlGenerator extends \Illuminate\Routing\UrlGenerator {
    protected $origin;

    public function __construct(\Illuminate\Routing\UrlGenerator $origin) {
        $this->origin = $origin;
        $this->setRequest($origin->getRequest());
    }

    /**
     * Generate the URL to an application asset.
     *
     * @param  string  $path
     * @param  bool|null  $secure
     * @return string
     */
    public function asset($path, $secure = null) {
        if (false && !empty(Session::get('s3theme-debug'))) {
            $config = Session::get('s3theme-config');
            $path = ltrim($path, '/');
            return sprintf('https://s3-%s.amazonaws.com/%s/%s/public/%s', Config('aws.region'), $config['bucket'], $config['theme'], $path);
        } else {
            return sprintf('%s?v=%s', $this->origin->asset($path, $secure), $this->version($path));
        }
    }

    /**
     * get asset's version
     */
    public function version($path) {
        $path = realpath(public_path($path));
        if (!empty($path)) {
            return md5_file($path);
        }
    }

    public function __call($method, $argv) {
        return $this->origin->{$method}(...$argv);
    }

    /** below starts to define for interface \Illuminate\Contracts\Routing\UrlGenerator  */
    public function full() {
        return $this->origin->full();
    }

    public function current() {
        return $this->origin->current();
    }

    public function previous($fallback = false) {
        return $this->origin->previous($fallback);
    }
    
    public function to($path, $extra = [], $secure = null) {
        return $this->origin->to($path, $extra = [], $secure = null);
    }
    
    public function secure($path, $parameters = []) {
        return $this->origin->secure($path, $parameters);
    }

    public function secureAsset($path) {
        return $this->asset($path, true);
    }

    public function route($name, $parameters = [], $absolute = true){
        return $this->origin->route($name, $parameters, $absolute);
    }
    
    public function action($action, $parameters = [], $absolute = true){
        return $this->origin->action($action, $parameters, $absolute);
    }

    public function setRootControllerNamespace($rootNamespace){
        return $this->origin->setRootControllerNamespace($rootNamespace);
    }

    public function assetFrom($root, $path, $secure = null)
    {
        return $this->origin->assetFrom($root, $path, $secure);
    }

    public function formatScheme($secure)
    {
        return $this->formatScheme($secure);
    }

    public function formatRoot($scheme, $root = null)
    {
        return $this->formatRoot($scheme, $root);
    }
}