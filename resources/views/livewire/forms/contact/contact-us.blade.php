<?php

	use App\Mail\ContactUsMail;
	use Illuminate\Support\Facades\Mail;
	use Livewire\Attributes\Layout;
	use Livewire\Volt\Component;

	new #[Layout('components.layouts.guest')] class extends Component {
		public array $form = [
			'name' => '',
			'email' => '',
			'phone' => null,
			'subject' => '',
			'message' => '',
		];

		public bool $sent = false;

		public function send():void
		{
			$validated = $this->validate($this->rules());

			Mail::to('dusty@peaceproxy.com')->send(new ContactUsMail(
				name: $validated['form']['name'],
				email: $validated['form']['email'],
				phone: $validated['form']['phone'] ?? null,
				subjectLine: $validated['form']['subject'],
				messageBody: $validated['form']['message'],
			));

			$this->reset('form');
			$this->sent = true;
		}

		protected function rules():array
		{
			return [
				'form.name' => ['required', 'string', 'max:255'],
				'form.email' => ['required', 'email', 'max:255'],
				'form.phone' => ['nullable', 'string', 'max:50'],
				'form.subject' => ['required', 'string', 'max:255'],
				'form.message' => ['required', 'string'],
			];
		}
	}
?>
<div>


	<div class="min-h-screen flex items-center">
		<div class="w-3xl mx-auto bg-dark-700 p-8 mt-8 rounded-lg">
			<x-logos.app-logo-icon class="mb-6" />
			<h1 class="text-2xl font-semibold text-white mb-6">Contact Us</h1>

			@if($sent)
				<div class="mb-6 rounded-md bg-green-600/20 border border-green-600 text-green-200 p-4">
					Thanks! Your message has been sent. We'll get back to you shortly.
				</div>
			@endif

			<form
					wire:submit="send"
					class="space-y-6">
				<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
					<x-input
							label="Your Name"
							wire:model="form.name"
							placeholder="Jane Doe"
							class="w-full" />

					<x-input
							type="email"
							label="Your Email"
							wire:model="form.email"
							placeholder="jane@example.com"
							class="w-full" />
				</div>

				<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
					<x-input
							label="Phone (optional)"
							wire:model="form.phone"
							placeholder="+1 415 555 0123"
							class="w-full" />

					<x-input
							label="Subject"
							wire:model="form.subject"
							placeholder="How can we help?"
							class="w-full" />
				</div>

				<div>
					<x-textarea
							label="Message"
							wire:model="form.message"
							placeholder="Type your message here..."
							class="w-full min-h-40" />
				</div>

				<div class="flex justify-end gap-4">
					<x-button
							color="secondary"
							href="/"
							type="button"
							class="uppercase">Cancel
					</x-button>
					<x-button
							type="submit"
							icon="paper-airplane"
							class="uppercase">Send Message
					</x-button>

				</div>
			</form>
		</div>
	</div>
</div>
