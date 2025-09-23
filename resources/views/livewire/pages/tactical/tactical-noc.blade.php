<?php

	use App\Models\Negotiation;
	use Illuminate\View\View;
	use Livewire\Attributes\Layout;
	use Livewire\Volt\Component;

	new #[Layout('layouts.negotiation'), \Livewire\Attributes\Title('Tactical NOC - Peace Proxy')] class extends Component {
		public ?Negotiation $negotiation = null;

		public function mount($negotiation):void
		{
			$this->negotiation = $negotiation;
		}

		public function rendering(View $view):void
		{
			// Pass negotiation to the layout so shared header/menus can use it
			$view->layoutData(['negotiation' => $this->negotiation]);
		}
	}

?>
<div
		wire:key="tactical-noc-root"
		class="text-white px-8 mb-16">
	<livewire:pages.tactical.noc-elements.top-cards :negotiation="$this->negotiation" />
	<div class="grid grid-cols-1 md:grid-cols-8 gap-4 mt-4">
		<div class="col-span-3 h-[calc(100vh-10rem)]">
			<livewire:pages.negotiation.chat.negotiation-chat :negotiationId="$this->negotiation->id" />
		</div>
		<div class="col-span-5 h-[calc(100vh-10rem)]">
			<livewire:pages.tactical.board.tactical-board :negotiationId="$this->negotiation->id" />
		</div>
	</div>
</div>
