<?php

	use App\Models\Negotiation;
	use Livewire\Volt\Component;

	new class extends Component {
		public Negotiation $negotiation;

		public function mount($negotiation)
		{
			$this->negotiation = $negotiation;
		}
	}

?>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 h-[13rem]">
	<livewire:pages.negotiation.noc-elements.subject.subject-card :negotiation="$this->negotiation" />
	<livewire:pages.negotiation.noc-elements.negotiation-card :negotiationId="$this->negotiation->id" />
</div>

