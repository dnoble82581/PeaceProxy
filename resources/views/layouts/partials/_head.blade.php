<!-- Google tag (gtag.js) For analytics -->
@if(app()->environment('production'))
	<script
			async
			src="https://www.googletagmanager.com/gtag/js?id=G-4ERSE25W2Q"></script>
	<script>
		window.dataLayer = window.dataLayer || []

		function gtag () {dataLayer.push(arguments)}

		gtag('js', new Date())

		gtag('config', 'G-4ERSE25W2Q', {
			'cookie_flags': 'SameSite=None;Secure;Partitioned'
		})
	</script>
@endif
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
		href="{{ asset('favicon.png') }}" />

<link
		rel="stylesheet"
		href="https://use.typekit.net/ccn6txi.css">

<link
		rel="icon"
		href="{{ asset('assets/favicon.png') }}"
		type="image/png">
@if(Route::is('negotiation.tactical-noc'))
	<script
			async
			src="https://maps.googleapis.com/maps/api/js?key={{config('services.maps.js_key')}}&callback=console.debug&libraries=maps,marker&v=beta&loading=async">
	</script>
@endif
{{-- Styles --}}
@vite(['resources/js/app.js', 'resources/css/app.css'])
@livewireStyles
@stack('head')