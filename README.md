# Livewire Range Slider

This package provides a simple and convenient range slider component for Laravel Livewire applications, utilizing noUiSlider for a smooth and responsive user experience.


## Installation

First, require the package using Composer:

```bash
composer require sadam/livewire-range-slider
```
After requiring the package, you'll need to install noUiSlider and set it up in your project.

Step 1: Install noUiSlider
Install noUiSlider via npm:

```bash
npm install nouislider
```

Step 2: Update app.js
After installing noUiSlider, import it and attach it to the window object in your resources/js/app.js file:

```bash
import noUiSlider from 'nouislider';
import 'nouislider/dist/nouislider.css';
window.noUiSlider = noUiSlider;
```

Step 3: Compile Assets
Once you've made the necessary updates to app.js, be sure to compile your assets:

```bash
npm run dev
```

Usage
After following the installation steps, you can start using the range slider in your Livewire components. Simply include the provided Blade component in your views:

```bash
  <x-range-slider :min="0" :max="100" :step="1" wire:model="values" />
```

## Important Note
Make sure you have a $values array property in your Livewire component. This array should have at least one element, but you can include as many elements as you need for multiple slider handles. For example:

```bash
public $values = [50]; // For a single handle

// or

public $values = [30, 70]; // For two handles

// or

public $values = [30, 50,90]; // For three handles
```
## Optimization for wire:model.live
The slider is optimized to work with wire:model.live if you prefer. This means that server requests will only be sent after the user has finished dragging the handle, reducing the number of requests and improving performance.

This package is open-sourced software licensed under the MIT license.
