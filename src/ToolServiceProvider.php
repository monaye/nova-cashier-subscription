<?php

namespace Monaye\NovaCashierSubscription;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

class ToolServiceProvider extends ServiceProvider
{

    public static $slug = 'nova-cashier-subscription';

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->booted(function () {
            $this->routes();
        });

        $this->publishes([
            __DIR__ . '/../config/' . self::$slug . '.php' => config_path(self::$slug . '.php'),
        ]);

        Nova::serving(function (ServingNova $event) {
            Nova::script(self::$slug, __DIR__ . '/../dist/js/tool.js');
            Nova::script(self::$slug . '-stripe', 'https://js.stripe.com/v3/');
            Nova::style(self::$slug, __DIR__ . '/../dist/css/tool.css');
        });
    }

    /**
     * Register the tool's routes.
     *
     * @return void
     */
    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Route::middleware(['nova'])
            ->prefix('nova-vendor/' . self::$slug)
            ->group(__DIR__ . '/../routes/api.php');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/' . self::$slug . '.php',
            self::$slug
        );
    }
}
