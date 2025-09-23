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
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
	<div class="h-64 md:h-[15rem] overflow-y-auto rounded-lg">
		<livewire:pages.tactical.noc-elements.card-situation :negotiation="$this->negotiation" />
	</div>
	<div class="h-64 md:h-[15rem] overflow-y-auto rounded-lg">
		<livewire:pages.tactical.noc-elements.card-target :negotiation="$this->negotiation" />
	</div>
	<div class="h-64 md:h-[15rem] overflow-y-auto rounded-lg">
		<livewire:pages.tactical.noc-elements.objectives-card :negotiationId="$this->negotiation->id" />
	</div>
	<div class="h-64 md:h-[15rem] overflow-y-auto rounded-lg">
		<livewire:pages.tactical.noc-elements.card-communications />
	</div>
</div>