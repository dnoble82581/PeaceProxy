@extends('layouts.guest')

@section('title', 'Server Error')

<div class="flex min-h-screen items-center justify-center px-6 py-16">
    <div class="w-full max-w-2xl text-center">
        <div class="mb-8 flex justify-center">
            @if (class_exists('BladeUI\Icons\Components\Svg'))
                <x-logos.app-logo-icon class="h-10 w-10 text-primary-600 dark:text-primary-400" />
            @endif
        </div>

        <h1 class="text-4xl font-bold tracking-tight text-dark-900 dark:text-white sm:text-5xl">Something went wrong</h1>
        <p class="mt-4 text-base text-dark-500 dark:text-dark-300">
            An unexpected error occurred and we couldn’t complete your request. Our team has been notified.
        </p>

        <div class="mt-8 flex items-center justify-center gap-3">
            <a
                href="{{ url()->previous() ?: url('/') }}"
                class="inline-flex items-center rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-dark-900">
                Go back
            </a>
            <a
                href="{{ url('/') }}"
                class="inline-flex items-center rounded-lg border border-dark-200 px-4 py-2 text-sm font-semibold text-dark-700 hover:bg-dark-50 dark:border-dark-700 dark:text-dark-100 dark:hover:bg-dark-800">
                Home
            </a>
        </div>

        <p class="mt-6 text-xs text-dark-400 dark:text-dark-500">
            Error code: 500
        </p>
    </div>
</div>
