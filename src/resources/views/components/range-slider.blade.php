<div
    wire:ignore
    x-data="{
        values: @entangle($attributes->wire('model')),
        isDragging: false
    }"
    x-init="
        initializeSlider($refs, () => values, (newValues) => values = newValues, {{ $min }}, {{ $max }}, 1)
    "
    {{ $attributes->merge(['class' => 'default-slider-class']) }}
>
    <div x-ref="slider"></div>
</div>

<script>
    function initializeSlider($refs, getValues, setValues, min, max, step) {
        const slider = noUiSlider.create($refs.slider, {
            start: getValues(),
            connect: true,
            range: {
                'min': [min],
                'max': [max]
            },
            step: step
        });

        let isDragging = false;

        slider.on('start', () => {
            isDragging = true;
        });

        slider.on('end', () => {
            if (isDragging) {
                setValues(slider.get(true)); // Update the values in the Alpine.js state
                isDragging = false;
            }
        });

        // Clean up event listeners when the component is destroyed
        $refs.slider.closest('[x-data]').addEventListener('destroy', () => {
            slider.off('start');
            slider.off('end');
            slider.off('update');
        });
    }
</script>


