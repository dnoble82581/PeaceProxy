<meta charset="UTF-8" />
<meta
		name="viewport"
		content="width=device-width, initial-scale=1.0" />

<meta
		name="csrf-token"
		content="{{ csrf_token() }}">


<title>{{ $title ?? 'Dashboard - Peace Proxy' }}</title>
<meta
		name="description"
		content="{{ $description ?? 'Law enforcement negotiation dashboard.' }}">
<link
		rel="icon"
		href="{{ asset('favicon.ico') }}" />

<link
		rel="stylesheet"
		href="https://use.typekit.net/ccn6txi.css">

{{-- Styles --}}
@vite(['resources/css/app.css', 'resources/js/app.js'])
@livewireStyles
@stack('head')