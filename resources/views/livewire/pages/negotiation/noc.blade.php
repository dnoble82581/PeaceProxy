<?php

	use App\Models\Negotiation;
	use App\Services\NegotiationUser\NegotiationUserUpdatingService;
	use Illuminate\Foundation\Application;
	use Illuminate\Http\RedirectResponse;
	use Illuminate\Routing\Redirector;
	use Livewire\Attributes\Layout;
	use Livewire\Volt\Component;
	use Illuminate\View\View;


	new #[Layout('layouts.negotiation')] class extends Component {

		public ?Negotiation $negotiation;

		public function mount($negotiation)
		{
			$this->negotiation = $negotiation;
		}

		public function rendering(View $view):void
		{
			$view->layoutData(['negotiation' => $this->negotiation]);
		}
	}

?>

<div class="text-white px-8 mb-16">
	<livewire:pages.negotiation.noc-elements.top-cards :negotiation="$this->negotiation" />
	<livewire:pages.negotiation.noc-elements.notifications />
	<div class="grid grid-cols-1 md:grid-cols-8 gap-4 mt-4">
		<div class="col-span-3 h-[calc(100vh-10rem)]">
			<livewire:pages.negotiation.chat.negotiation-chat :negotiationId="$this->negotiation->id" />
		</div>
		<div class="col-span-5 h-[calc(100vh-10rem)]">
			<livewire:pages.negotiation.board.negotiation-board :negotiationId="$this->negotiation->id" />
		</div>
	</div>
</div>
