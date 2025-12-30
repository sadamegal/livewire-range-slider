<?php

namespace Sadam\LivewireRangeSlider\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class RangeSlider extends Component
{
    public float $min;
    public float $max;
    public float $step;
    public bool $tooltips;
    public bool|array $pips;
    public string $theme;
    public string $size;
    public string $variant;
    public string $direction;
    public string $behaviour;

    public function __construct(
        float|int $min = null,
        float|int $max = null,
        float $step = null,
        bool $tooltips = null,
        bool|array|null $pips = null,
        string $theme = null,
        string $size = null,
        string $variant = null,
        string $direction = null,
        string $behaviour = null
    ) {
        $this->min = $min ?? config('livewire-range-slider.min', 1);
        $this->max = $max ?? config('livewire-range-slider.max', 100);
        $this->step = $step ?? config('livewire-range-slider.step', 1);
        $this->tooltips = $tooltips ?? config('livewire-range-slider.tooltips', false);
        $this->pips = $this->normalizePips($pips ?? config('livewire-range-slider.pips', false));
        $this->theme = $this->sanitizeString($theme ?? config('livewire-range-slider.theme', 'default'));
        $this->size = $this->validateSize($size ?? config('livewire-range-slider.size', 'medium'));
        $this->variant = $this->validateVariant($variant ?? config('livewire-range-slider.variant', 'square'));
        $this->direction = $this->validateDirection($direction ?? config('livewire-range-slider.direction', 'ltr'));
        $this->behaviour =$this->validateBehaviour($behaviour ?? config('livewire-range-slider.behaviour','tap'));
    }

    protected function normalizePips(bool|array $pips): bool|array
    {
        if (is_bool($pips)) {
            return $pips;
        }

        if (is_array($pips)) {
            // Filter to only numeric values and reindex
            $filtered = array_values(array_filter($pips, fn($v) => is_numeric($v)));
            return empty($filtered) ? false : $filtered;
        }

        return false;
    }

    protected function validateSize(string $size): string
    {
        $validSizes = ['small', 'medium', 'large'];
        return in_array($size, $validSizes, true) ? $size : 'medium';
    }

    protected function validateVariant(string $variant): string
    {
        $validVariants = ['square', 'rounded'];
        return in_array($variant, $validVariants, true) ? $variant : 'square';
    }

    protected function validateDirection(string $direction): string
    {
        $validDirections = ['ltr', 'rtl'];
        return in_array($direction, $validDirections, true) ? $direction : 'ltr';
    }

    protected function validateBehaviour(string $behaviour): string
    {
        $validBehaviours =
            [
                "drag",
                "drag-all",
                "tap",
                "fixed",
                "snap",
                "tap-drag",
                "unconstrained",
                "invert-connects",
                "none",
               // "unconstrained-invert-connects"
                ];
        return in_array($behaviour, $validBehaviours, true) ? $behaviour : 'invert-connects';
    }

    protected function sanitizeString(string $value): string
    {
        // Only allow alphanumeric, hyphens, and underscores
        $sanitized = preg_replace('/[^a-zA-Z0-9\-_]/', '', $value);
        return $sanitized !== '' ? $sanitized : 'default';
    }

    public function render(): View|Closure|string
    {
        return view('livewire-range-slider::components.range-slider');
    }
}
