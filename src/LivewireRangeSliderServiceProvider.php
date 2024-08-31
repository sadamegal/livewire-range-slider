<?php

namespace Sadam\LivewireRangeSlider;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\Support\Facades\Blade;

class LivewireRangeSliderServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        // Load views from the package's resources directory
        $this->loadViewsFrom(__DIR__.'/resources/views', 'livewire-range-slider');

        // Register components after loading views
        $this->configureComponents();

        // Publish bundled assets
        $this->publishes([
            __DIR__.'/../public' => public_path('vendor/livewire-range-slider'),
        ], 'public');
         // Publishing the views
    $this->publishes([
        __DIR__.'/resources/views' => resource_path('views/vendor/livewire-range-slider'),
    ], 'views');
    }

    /**
     * Register the component.
     */
    protected function configureComponents()
    {
        $this->callAfterResolving(BladeCompiler::class, function () {
            $this->registerComponent('range-slider');
        });
    }

    /**
     * Register a specific Blade component.
     */
    protected function registerComponent(string $component)
    {
        Blade::component($component, \Sadam\LivewireRangeSlider\View\Components\RangeSlider::class);
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        //
    }
}
