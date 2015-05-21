<?php

namespace LaravelCommode\Common;

use Illuminate\Foundation\AliasLoader;
use LaravelCommode\Common\Constants\ServiceShortCuts;
use LaravelCommode\Common\GhostService\GhostService;
use LaravelCommode\Common\GhostService\GhostServices;
use LaravelCommode\Common\Resolver\Resolver;

/**
 * Class CommodeCommonServiceProvider
 *
 * Is a common service for all laravel-commode packages.
 * It binds Resolver and GhostService manager.
 *
 * @author Volynov Andrew
 * @package LaravelCommode\Common
 */
class CommodeCommonServiceProvider extends GhostService
{
    protected $aliases = [
        'CommodeResolver' => 'LaravelCommode\Common\Facades\Resolver'
    ];
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [ServiceShortCuts::GHOST_SERVICE, ServiceShortCuts::RESOLVER_SERVICE];
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('laravel-commode/common', 'commode-common', __DIR__.'/../../');
    }

    /**
     * Will be triggered when the app's 'booting' event is triggered
     */
    protected function launching()
    {

    }

    /**
     * Triggered when service is being registered
     */
    protected function registering()
    {
        $this->app->bindShared(ServiceShortCuts::RESOLVER_SERVICE, function () {
            return new Resolver($this->app);
        });

        $this->app->bindShared(ServiceShortCuts::GHOST_SERVICE, function () {
            return new GhostServices($this->app);
        });

        $this->app->bind(ServiceShortCuts::CORE_INITIALIZED, true);

        $this->app->booting(function () {
            $loader = AliasLoader::getInstance();

            foreach ($this->aliases as $facade => $facadeClass) {
                $loader->alias($facade, $facadeClass);
            }
        });
    }
}
