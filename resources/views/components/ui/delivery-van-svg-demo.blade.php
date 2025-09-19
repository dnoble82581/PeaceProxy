<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Delivery Van SVG Component Demo
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Default Usage</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Using default color and size.
                    </p>
                    <div class="mt-4 flex items-center space-x-4">
                        <x-ui.delivery-van-svg />
                        <x-button>
                            <x-ui.delivery-van-svg class="mr-2" />
                            Delivery
                        </x-button>
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Custom Colors</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Using custom colors for the SVG.
                    </p>
                    <div class="mt-4 flex items-center space-x-4">
                        <x-ui.delivery-van-svg color="#4f46e5" />
                        <x-button color="indigo">
                            <x-ui.delivery-van-svg color="white" class="mr-2" />
                            Delivery
                        </x-button>
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Different Sizes</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Customizing the size of the SVG.
                    </p>
                    <div class="mt-4 flex items-center space-x-4">
                        <x-ui.delivery-van-svg width="16" height="16" />
                        <x-ui.delivery-van-svg width="24" height="24" />
                        <x-ui.delivery-van-svg width="32" height="32" />
                        <x-ui.delivery-van-svg width="48" height="48" />
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Button Examples</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Using the SVG in different button styles.
                    </p>
                    <div class="mt-4 flex flex-wrap items-center gap-4">
                        <x-button color="blue">
                            <x-ui.delivery-van-svg class="mr-2" />
                            Delivery
                        </x-button>
                        
                        <x-button color="green" sm>
                            <x-ui.delivery-van-svg width="16" height="16" class="mr-1" />
                            Small
                        </x-button>
                        
                        <x-button color="red" lg>
                            <x-ui.delivery-van-svg width="28" height="28" class="mr-2" />
                            Large
                        </x-button>
                        
                        <x-button color="amber" flat>
                            <x-ui.delivery-van-svg class="mr-2" />
                            Flat
                        </x-button>
                        
                        <x-button color="purple" outline>
                            <x-ui.delivery-van-svg class="mr-2" />
                            Outline
                        </x-button>
                        
                        <x-button.circle color="cyan">
                            <x-ui.delivery-van-svg />
                        </x-button.circle>
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Tailwind Classes</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Using Tailwind CSS classes for styling.
                    </p>
                    <div class="mt-4 flex items-center space-x-4">
                        <x-ui.delivery-van-svg class="text-blue-500" />
                        <x-ui.delivery-van-svg class="text-green-500" />
                        <x-ui.delivery-van-svg class="text-red-500" />
                        <x-ui.delivery-van-svg class="text-yellow-500" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>