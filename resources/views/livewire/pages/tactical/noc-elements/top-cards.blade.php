<?php

	use App\Models\Negotiation;
	use Livewire\Volt\Component;

	new class extends Component {
		public Negotiation $negotiation;

		public function mount($negotiation):void
		{
			$this->negotiation = $negotiation;
		}
	};

?>
<div class="grid grid-cols-1 lg:grid-cols-4 gap-4 h-[15rem]">
	<livewire:pages.tactical.noc-elements.card-situation :negotiation="$this->negotiation" />
	<livewire:pages.tactical.noc-elements.card-target :negotiation="$this->negotiation" />
	<livewire:pages.tactical.noc-elements.objectives-card :negotiationId="$this->negotiation->id" />
	<livewire:pages.tactical.noc-elements.card-communications />
</div>