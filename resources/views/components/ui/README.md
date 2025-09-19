# UI Components

This directory contains reusable UI components for the PeaceProxy application.

## Delivery Van SVG Component

The `delivery-van-svg.blade.php` component provides a simple, customizable delivery van icon that can be used in buttons or as a standalone icon.

### Usage

```blade
<x-ui.delivery-van-svg />
```

### Customization Options

The component accepts the following props for customization:

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `color` | String | 'currentColor' | Controls the stroke color of the SVG |
| `width` | String | '24' | Controls the width of the SVG |
| `height` | String | '24' | Controls the height of the SVG |
| `class` | String | '' | Additional CSS classes to apply to the SVG |

### Examples

#### Default Usage

```blade
<x-ui.delivery-van-svg />
```

#### Custom Color

```blade
<x-ui.delivery-van-svg color="#4f46e5" />
```

#### Custom Size

```blade
<x-ui.delivery-van-svg width="32" height="32" />
```

#### Using in Buttons

```blade
<x-button>
    <x-ui.delivery-van-svg class="mr-2" />
    Delivery
</x-button>
```

#### Using with Tailwind CSS Classes

```blade
<x-ui.delivery-van-svg class="text-blue-500" />
```

### Demo

A comprehensive demo of the component with various customization options is available in `delivery-van-svg-demo.blade.php`.