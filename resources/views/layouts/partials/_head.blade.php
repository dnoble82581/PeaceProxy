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

<script>
	(g => {
		var h, a, k, p = 'The Google Maps JavaScript API', c = 'google', l = 'importLibrary', q = '__ib__',
			m = document, b = window
		b = b[c] || (b[c] = {})
		var d = b.maps || (b.maps = {}), r = new Set, e = new URLSearchParams,
			u = () => h || (h = new Promise(async (f, n) => {
				await (a = m.createElement('script'))
				e.set('key', "{{ config('services.maps.js_key') }}")
				e.set('v', 'weekly')
				e.set('callback', c + '.maps.' + q)
				a.src = `https://maps.${c}apis.com/maps/api/js?` + e
				d[q] = f
				a.onerror = () => h = n(Error(p + ' could not load.'))
				a.nonce = m.querySelector('script[nonce]')?.nonce || ''
				m.head.append(a)
			}))
		d[l] ? console.warn(p + ' only loads once. Ignoring:', g) : d[l] = (f, ...n) => r.add(f) && u().then(() => d[l](f, ...n))
	})({})
</script>

{{-- Styles --}}
@vite(['resources/js/app.js', 'resources/css/app.css'])
@livewireStyles
@stack('head')