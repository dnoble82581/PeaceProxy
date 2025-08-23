<?php

	use App\DTOs\Call\CallCreateDTO;

	// <- DTO\Calls
	use App\Services\Call\CallPlacementService;

	// <- Services\Calls
	use Livewire\Attributes\Layout;
	use Livewire\Volt\Component;

	new #[Layout('layouts.negotiation')] class extends Component {
		public string $to = '';
		public ?string $lastSid = null;
		public ?string $lastStatus = null;

		public function rules()
		{
			return ['to' => ['required', 'string']];
		}

		public function callNow(CallPlacementService $placer)
		{
//			$this->validate();
//
			$dto = CallCreateDTO::fromArray([
				'tenant_id' => tenant()->id,
				'to' => $this->to,
				'from' => config('twilio.from'),
				'created_by' => auth()->id(),
			]);

			$call = $placer->dialSubject($dto, '+13195947290');

//			$this->lastSid = $call->call_sid;
//			$this->lastStatus = $call->status;
//			$this->dispatch('notify', body: 'Dialing...');
		}
	};
?>

<div class="max-w-md mx-auto p-6 rounded-2xl border shadow-sm space-y-4">
	<div class="space-y-1">
		<label class="text-sm font-medium">Phone number (E.164)</label>
		<input
				type="tel"
				wire:model="to"
				placeholder="+15551234567"
				class="w-full rounded-xl border px-3 py-2 outline-none focus:ring text-black" />
		@error('to')
		<div class="text-red-600 text-sm">{{ $message }}</div> @enderror
	</div>

	<button
			type="button"
			{{-- type=button if inside a <form> --}}
			wire:click="callNow"
			class="w-full rounded-2xl px-4 py-2 font-semibold border hover:shadow">
		Call this number
	</button>

	{{--	@if($lastSid)--}}
	{{--		<div class="text-xs text-gray-600">--}}
	{{--			Last Call SID: <span class="font-mono">{{ $lastSid }}</span> ({{ $lastStatus }})--}}
	{{--		</div>--}}
	{{--	@endif--}}
</div>
