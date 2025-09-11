import Echo from 'laravel-echo'

import Pusher from 'pusher-js'

window.Pusher = Pusher

window.Echo = new Echo({
	broadcaster: 'reverb',
	key: import.meta.env.VITE_REVERB_APP_KEY,                  // laravel-herd
	wsHost: import.meta.env.VITE_REVERB_HOST,                  // localhost
	wsPort: Number(import.meta.env.VITE_REVERB_PORT),          // 8080
	wssPort: Number(import.meta.env.VITE_REVERB_PORT),         // 8080
	forceTLS: false,                                           // HTTP mode
	enabledTransports: ['ws'],
	csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
	authEndpoint: '/broadcasting/auth',
	auth: {
		headers: {
			'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
		},
	},
})
