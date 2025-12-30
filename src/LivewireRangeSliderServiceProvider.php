<?php

namespace Sadam\LivewireRangeSlider;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Sadam\LivewireRangeSlider\View\Components\RangeSlider;

class LivewireRangeSliderServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/livewire-range-slider.php',
            'livewire-range-slider'
        );
    }

    public function boot(): void
    {
        $this->loadViewsFrom(
            __DIR__.'/../resources/views',
            'livewire-range-slider'
        );

        Blade::component('range-slider', RangeSlider::class);

        $this->publishes([
            __DIR__.'/../resources/dist/nouislider.min.js' =>
                public_path('vendor/livewire-range-slider/nouislider.min.js'),

            __DIR__.'/../resources/dist/nouislider.min.css' =>
                public_path('vendor/livewire-range-slider/nouislider.min.css'),

            __DIR__.'/../resources/css/range-slider.css' =>
                public_path('vendor/livewire-range-slider/range-slider.css'),
        ], 'livewire-range-slider-assets');

        $this->publishes([
            __DIR__.'/../config/livewire-range-slider.php' =>
                config_path('livewire-range-slider.php'),
        ], 'livewire-range-slider-config');

    }
}
