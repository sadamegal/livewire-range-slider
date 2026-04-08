@props([
    'min',
    'max',
    'step',
    'tooltips',
    'pips',
    'theme',
    'size',
    'variant',
    'direction',
    'behaviour',
    'values',
])

@php
    $wireModel = $attributes->wire('model');
    if (! $wireModel) {
        throw new \InvalidArgumentException('RangeSlider requires a wire:model binding.');
    }

    $wireModifier = $wireModel->hasModifier('live') ? 'live' : ($wireModel->hasModifier('blur') ? 'blur' : 'defer');
    $wireProperty = $wireModel->value();
@endphp

<div
    wire:ignore
    x-data="rangeSlider({
        property: @js($wireProperty),
        modifier: @js($wireModifier),
        min: {{ $values ? min($values) : $min }},
        max: {{ $values ? max($values) : $max }},
        step: {{ $step }},
        tooltips: {{ $tooltips ? 'true' : 'false' }},
        pips: @js($pips),
        direction: @js($direction),
        behaviour: @js($behaviour),
        values: @js($values),
    })"
    class="range-slider range-slider-{{ $theme }} range-slider-{{ $size }} range-slider-{{ $variant }}"
    {{ $attributes->except(['wire:model', 'wire:model.live', 'wire:model.blur']) }}

>
    <div x-ref="slider"></div>
</div>
@once
@assets
<link rel="stylesheet" href="{{asset('/vendor/livewire-range-slider/nouislider.min.css')}}">
<link rel="stylesheet" href="{{asset('/vendor/livewire-range-slider/range-slider.css')}}">
<script src="{{asset('/vendor/livewire-range-slider/nouislider.min.js')}}"></script>
@endassets
@endonce

@script
<script>
    Alpine.data('rangeSlider', (config) => ({
        slider: null,
        isUpdatingFromWire: false,

        isKeyboard: false,
        keyboardTimer: null,
        keyboardSetTimer: null,
        isKeyboardActive: false,
        keyboardIdleTimer: null,
        pendingKeyboardValues: null,


        async init() {
            try {
                if (!this.$refs.slider || typeof noUiSlider === 'undefined') {
                    console.error('Range slider: Missing slider element or noUiSlider library');
                    return;
                }

                // Get initial value from Livewire
                const initialValue = await this.$wire.get(config.property);
                const isRange = Array.isArray(initialValue);
                const start = isRange ? initialValue : [initialValue ?? config.min];

                // Create slider
                this.slider = noUiSlider.create(this.$refs.slider, {
                    start,
                    behaviour: config.behaviour,  // ← Fixed spelling
                    connect: isRange ? true : [true, false],
                    step: config.step,
                    range: config.values
                        ? Object.fromEntries(
                            config.values.map((v, i) => {
                                const pct = ((v - config.min) / (config.max - config.min)) * 100;
                                const key = i === 0 ? 'min' : i === config.values.length - 1 ? 'max' : `${pct}%`;
                                return [key, v];
                            })
                        )
                        : { min: config.min, max: config.max },
                    snap: !!config.values,
                    tooltips: config.tooltips,
                    pips: this.getPipsConfig(config.pips),
                    direction: config.direction,
                });

                this.$nextTick(() => {
                    this.$refs.slider
                        .querySelectorAll('.noUi-handle')
                        .forEach(handle => {
                            handle.addEventListener('keydown', () => {
                                this.isKeyboardActive = true;
                                clearTimeout(this.keyboardIdleTimer);
                            });

                            handle.addEventListener('keyup', () => {
                                clearTimeout(this.keyboardIdleTimer);
                                this.keyboardIdleTimer = setTimeout(() => {
                                    this.isKeyboardActive = false;

                                    // commit once keyboard stops
                                    if (this.pendingKeyboardValues) {
                                        this.updateLivewire(this.pendingKeyboardValues, true);
                                        this.pendingKeyboardValues = null;
                                    }
                                }, 320);
                            });
                        });
                });


                // Set up Livewire updates based on modifier
                this.setupLivewireUpdates(config.modifier);

                // Watch for Livewire property changes
                this.$wire.$watch(config.property, (value) => {
                    this.$nextTick(() => {
                        this.updateSliderFromWire(value);
                    });
                });

            } catch (error) {
                console.error('Range slider initialization error:', error);
            }
        },

        snapToAllowed(numericValue) {
            if (!config.values || !Array.isArray(config.values)) {
                return numericValue;
            }
            return config.values.reduce((prev, curr) =>
                Math.abs(curr - numericValue) < Math.abs(prev - numericValue) ? curr : prev
            );
        },

        setupLivewireUpdates(modifier) {
            if (modifier === 'live') {
                // Update immediately on drag end
                this.slider.on('set', values => {
                    if (this.isKeyboardActive) {
                        // store latest value, do NOT sync yet
                        this.pendingKeyboardValues = values;
                        return;
                    }

                    // drag / touch → immediate commit
                    this.updateLivewire(values, true);
                });


            } else if (modifier === 'blur') {
                // Update when handle loses focus
                this.$nextTick(() => {
                    const handles = this.$refs.slider.querySelectorAll('.noUi-handle');
                    handles.forEach(handle => {
                        handle.addEventListener('blur', () => {
                            if (!this.isUpdatingFromWire) {
                                const values = this.slider.get();
                                this.updateLivewire(values, true);
                            }
                        });
                    });
                });
            } else {
                // Default: update on change (when dragging stops)
                this.slider.on('set', (values) => {
                    this.updateLivewire(values, false);
                });
            }
        },

        updateLivewire(values, isLive) {
            if (this.isUpdatingFromWire) return;

            const numericValues = Array.isArray(values)
                ? values.map(v => this.snapToAllowed(parseFloat(v)))
                : [this.snapToAllowed(parseFloat(values))];

            const result = numericValues.length === 1 ? numericValues[0] : numericValues;

            this.$wire.set(config.property, result, isLive);
        },

        updateSliderFromWire(value) {
            if (!this.slider || value == null || this.isUpdatingFromWire) return;

            this.isUpdatingFromWire = true;

            try {
                const values = Array.isArray(value) ? value : [value];
                this.slider.set(values);
            } catch (error) {
                console.error('Range slider update error:', error);
            } finally {
                this.isUpdatingFromWire = false;
            }
        },

        getPipsConfig(pips) {
            if (pips === false) return false;
            if (pips === true) return { mode: 'range', density: 3 };
            if (Array.isArray(pips) && pips.length > 0) {
                return { mode: 'values', values: pips };
            }
            return false;
        },


        //Clean up when component is destroyed
        destroy() {
            if (this.slider) {
                this.slider.destroy();
                this.slider = null;
            }
        }
    }));
</script>
@endscript
