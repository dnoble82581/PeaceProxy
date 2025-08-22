@props([
    'title' => null,
    'subtitle' => null,
    'icon' => null,
    'iconColor' => null,
    'blur' => null,
    'persistent' => null,
    'size' => null,
    'center' => null,
    'scrollable' => null,
    'scrollbar' => null,
    'wire' => null,
    'id' => null,
    'z' => null,
    'overflow' => null,
])

<x-modal
    :title="$title"
    :subtitle="$subtitle"
    :icon="$icon"
    :icon-color="$iconColor"
    :blur="$blur"
    :persistent="$persistent"
    :size="$size"
    :center="$center"
    :scrollable="$scrollable"
    :scrollbar="$scrollbar"
    :wire="$wire"
    :id="$id"
    :z="$z"
    :overflow="$overflow"
    class="dark-modal"
    {{ $attributes }}
>
    {{ $slot }}
</x-modal>

@pushOnce('styles')
<style>
    /* Dark mode styles for modal */
    .dark .dark-modal [x-data="modal"] > div:first-child {
        @apply bg-dark-900 bg-opacity-75;
    }
    
    .dark .dark-modal [x-data="modal"] > div:first-child > div {
        @apply bg-dark-700 border-dark-600 text-gray-200;
    }
    
    .dark .dark-modal [x-data="modal"] .modal-title {
        @apply text-gray-200;
    }
    
    .dark .dark-modal [x-data="modal"] .modal-subtitle {
        @apply text-gray-300;
    }
    
    .dark .dark-modal [x-data="modal"] .modal-close-button {
        @apply text-gray-400 hover:text-gray-200;
    }
</style>
@endPushOnce