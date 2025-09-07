<!-- Google tag (gtag.js) -->
<script
		async
		src="https://www.googletagmanager.com/gtag/js?id=G-4ERSE25W2Q"></script>
<script>
	window.dataLayer = window.dataLayer || []

	function gtag () {dataLayer.push(arguments)}

	gtag('js', new Date())

	gtag('config', 'G-4ERSE25W2Q')
</script>
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

<link
		rel="icon"
		href="{{ asset('assets/favicon.png') }}"
		type="image/png">

{{-- Styles --}}
@vite(['resources/css/app.css', 'resources/js/app.js'])
@livewireStyles
@stack('head')