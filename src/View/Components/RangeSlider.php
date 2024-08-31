<?php

namespace Sadam\LivewireRangeSlider\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class RangeSlider extends Component
{
    public $min;
    public $max;

    /**
     * Create a new component instance.
     */
    public function __construct($min = 0, $max = 100)
    {
        $this->min = $min;
        $this->max = $max;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('livewire-range-slider::components.range-slider');
    }
}
