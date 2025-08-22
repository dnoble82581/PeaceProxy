<?php

	use App\Models\Negotiation;
	use App\Models\Subject;
	use Livewire\Attributes\Layout;
	use Livewire\Volt\Component;
	use Illuminate\View\View;

	new #[Layout('layouts.negotiation')] class extends Component {
		public Subject $subject;
		public Negotiation $negotiation;

		public function mount(Subject $subject, Negotiation $negotiation)
		{
			$this->subject = $subject;
			$this->negotiation = $negotiation;

			// Eager load images to avoid N+1 query issues
			$this->subject->load('images');
		}

		public function rendering(View $view):void
		{
			$view->layoutData(['title' => $this->subject->name]);
		}
	}

?>

<div class="p-4">
	<div class="bg-white dark:bg-dark-800 rounded-lg shadow-md p-6">
		<div class="flex items-center justify-between mb-6">
			<div class="flex items-center space-x-4">
				<img
						src="{{ $subject->primaryImage() }}"
						alt="{{ $subject->name }}"
						class="rounded-full h-20 w-20 object-cover"
				/>
				<div>
					<h1 class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ $subject->name }}</h1>
					<p class="text-gray-600 dark:text-gray-400">
						{{ $subject->status?->label() ?? 'Unknown Status' }}
					</p>
				</div>
			</div>
			<div>
				<x-button
						sm
						icon="pencil-square"
						primary
						href="{{ route('subject.edit', ['subject' => $subject, 'tenantSubdomain' => tenant()->subdomain]) }}"
						wire:navigate
				>
					Edit Subject
				</x-button>
				<x-button
						color="secondary"
						sm
						icon="arrow-long-left"
						:href="route('negotiation-noc', ['tenantSubdomain' => tenant()->subdomain, 'negotiation' => $negotiation])"
						wire:navigate>
					Back
				</x-button>
			</div>
		</div>

		<div x-data="{ activeTab: 'basic' }">
			<!-- Tabs -->
			<div class="border-b border-gray-200 dark:border-gray-700 mb-6">
				<nav class="-mb-px flex space-x-8">
					<button
							@click="activeTab = 'basic'"
							:class="{'border-primary-500 text-primary-600 dark:text-primary-400': activeTab === 'basic', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== 'basic'}"
							class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
					>
						<x-icon
								name="user"
								class="h-4 w-4 inline-block mr-1" />
						Basic Information
					</button>
					<button
							@click="activeTab = 'contact'"
							:class="{'border-primary-500 text-primary-600 dark:text-primary-400': activeTab === 'contact', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== 'contact'}"
							class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
					>
						<x-icon
								name="phone"
								class="h-4 w-4 inline-block mr-1" />
						Contact Information
					</button>
					<button
							@click="activeTab = 'employment'"
							:class="{'border-primary-500 text-primary-600 dark:text-primary-400': activeTab === 'employment', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== 'employment'}"
							class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
					>
						<x-icon
								name="briefcase"
								class="h-4 w-4 inline-block mr-1" />
						Employment
					</button>
					<button
							@click="activeTab = 'history'"
							:class="{'border-primary-500 text-primary-600 dark:text-primary-400': activeTab === 'history', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== 'history'}"
							class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
					>
						<x-icon
								name="document-text"
								class="h-4 w-4 inline-block mr-1" />
						History & Risk
					</button>
					<button
							@click="activeTab = 'status'"
							:class="{'border-primary-500 text-primary-600 dark:text-primary-400': activeTab === 'status', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== 'status'}"
							class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
					>
						<x-icon
								name="flag"
								class="h-4 w-4 inline-block mr-1" />
						Status
					</button>
					<button
							@click="activeTab = 'images'"
							:class="{'border-primary-500 text-primary-600 dark:text-primary-400': activeTab === 'images', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== 'images'}"
							class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
					>
						<x-icon
								name="photo"
								class="h-4 w-4 inline-block mr-1" />
						Images
					</button>
				</nav>
			</div>

			<!-- Tab Content -->
			<div
					x-show="activeTab === 'basic'"
					class="space-y-6">
				<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
					<!-- Removed redundant info card -->

					<div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
						<span class="text-sm font-medium text-gray-500 dark:text-gray-400 block mb-1">Name</span>
						<p class="text-gray-900 dark:text-gray-100">{{ $subject->name ?? 'Unknown' }}</p>
					</div>
					<div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
						<span class="text-sm font-medium text-gray-500 dark:text-gray-400 block mb-1">Age</span>
						<p class="text-gray-900 dark:text-gray-100">{{ $subject->subjectAge() ?? 'Unknown' }}</p>
					</div>
					<div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
						<span class="text-sm font-medium text-gray-500 dark:text-gray-400 block mb-1">Gender</span>
						<p class="text-gray-900 dark:text-gray-100">{{ $subject->gender ?? 'Unknown' }}</p>
					</div>
					<div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
						<span class="text-sm font-medium text-gray-500 dark:text-gray-400 block mb-1">Height</span>
						<p class="text-gray-900 dark:text-gray-100">{{ $subject->height ?? 'Unknown' }}</p>
					</div>
					<div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
						<span class="text-sm font-medium text-gray-500 dark:text-gray-400 block mb-1">Weight</span>
						<p class="text-gray-900 dark:text-gray-100">{{ $subject->weight ? $subject->weight . ' lbs' : 'Unknown' }}</p>
					</div>
					<div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
						<span class="text-sm font-medium text-gray-500 dark:text-gray-400 block mb-1">Hair Color</span>
						<p class="text-gray-900 dark:text-gray-100">{{ $subject->hair_color ?? 'Unknown' }}</p>
					</div>
					<div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
						<span class="text-sm font-medium text-gray-500 dark:text-gray-400 block mb-1">Eye Color</span>
						<p class="text-gray-900 dark:text-gray-100">{{ $subject->eye_color ?? 'Unknown' }}</p>
					</div>
					<div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
						<span class="text-sm font-medium text-gray-500 dark:text-gray-400 block mb-1">Identifying Features</span>
						<p class="text-gray-900 dark:text-gray-100">{{ $subject->identifying_features ?? 'None' }}</p>
					</div>
					<div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
						<span class="text-sm font-medium text-gray-500 dark:text-gray-400 block mb-1">Date of Birth</span>
						<p class="text-gray-900 dark:text-gray-100">{{ $subject->date_of_birth ? $subject->date_of_birth->format('M d, Y') : 'Unknown' }}</p>
					</div>
				</div>

				@if($subject->alias && count($subject->alias) > 0)
					<div class="mt-6">
						<h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">Aliases</h3>
						<div class="flex flex-wrap gap-2">
							@foreach($subject->alias as $alias)
								<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-700 dark:text-primary-100">
									{{ $alias }}
								</span>
							@endforeach
						</div>
					</div>
				@endif
			</div>

			<div
					x-show="activeTab === 'contact'"
					class="space-y-6">
				<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
					<div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
						<span class="text-sm font-medium text-gray-500 dark:text-gray-400 block mb-1">Phone</span>
						<p class="text-gray-900 dark:text-gray-100">{{ $subject->phone ?? 'None' }}</p>
					</div>
					<div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
						<span class="text-sm font-medium text-gray-500 dark:text-gray-400 block mb-1">Email</span>
						<p class="text-gray-900 dark:text-gray-100">{{ $subject->email ?? 'None' }}</p>
					</div>
					<div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
						<span class="text-sm font-medium text-gray-500 dark:text-gray-400 block mb-1">Address</span>
						<p class="text-gray-900 dark:text-gray-100">{{ $subject->address ?? 'Unknown' }}</p>
					</div>
					<div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
						<span class="text-sm font-medium text-gray-500 dark:text-gray-400 block mb-1">City</span>
						<p class="text-gray-900 dark:text-gray-100">{{ $subject->city ?? 'Unknown' }}</p>
					</div>
					<div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
						<span class="text-sm font-medium text-gray-500 dark:text-gray-400 block mb-1">State</span>
						<p class="text-gray-900 dark:text-gray-100">{{ $subject->state ?? 'Unknown' }}</p>
					</div>
					<div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
						<span class="text-sm font-medium text-gray-500 dark:text-gray-400 block mb-1">ZIP Code</span>
						<p class="text-gray-900 dark:text-gray-100">{{ $subject->zip ?? 'Unknown' }}</p>
					</div>
					<div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
						<span class="text-sm font-medium text-gray-500 dark:text-gray-400 block mb-1">Country</span>
						<p class="text-gray-900 dark:text-gray-100">{{ $subject->country ?? 'Unknown' }}</p>
					</div>
				</div>
			</div>

			<div
					x-show="activeTab === 'employment'"
					class="space-y-6">
				<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
					<div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
						<span class="text-sm font-medium text-gray-500 dark:text-gray-400 block mb-1">Occupation</span>
						<p class="text-gray-900 dark:text-gray-100">{{ $subject->occupation ?? 'Unknown' }}</p>
					</div>
					<div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
						<span class="text-sm font-medium text-gray-500 dark:text-gray-400 block mb-1">Employer</span>
						<p class="text-gray-900 dark:text-gray-100">{{ $subject->employer ?? 'Unknown' }}</p>
					</div>
				</div>
			</div>

			<div
					x-show="activeTab === 'history'"
					class="space-y-6">
				<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
					<div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
						<h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">Mental Health History</h3>
						<p class="text-gray-700 dark:text-gray-300">{{ $subject->mental_health_history ?? 'No known history' }}</p>
					</div>

					<div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
						<h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">Criminal History</h3>
						<p class="text-gray-700 dark:text-gray-300">{{ $subject->criminal_history ?? 'No known history' }}</p>
					</div>

					<div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
						<h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">Substance Abuse
						                                                                      History</h3>
						<p class="text-gray-700 dark:text-gray-300">{{ $subject->substance_abuse_history ?? 'No known history' }}</p>
					</div>

					<div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
						<h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">Known Weapons</h3>
						<p class="text-gray-700 dark:text-gray-300">{{ $subject->known_weapons ?? 'None known' }}</p>
					</div>
				</div>

				@if($subject->risk_factors && count($subject->risk_factors) > 0)
					<div class="mt-6">
						<h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">Risk Factors</h3>
						<div class="flex flex-wrap gap-2">
							@foreach($subject->risk_factors as $risk)
								<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100">
									{{ $risk }}
								</span>
							@endforeach
						</div>
					</div>
				@endif

				@if($subject->notes)
					<div class="mt-6 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
						<h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">Notes</h3>
						<p class="text-gray-700 dark:text-gray-300">{{ $subject->notes }}</p>
					</div>
				@endif
			</div>

			<div
					x-show="activeTab === 'status'"
					class="space-y-6">
				<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
					<div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
						<h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">Current Mood</h3>
						<div class="flex items-center">
							<span class="text-3xl mr-2">{{ $subject->current_mood?->icon() }}</span>
							<span class="text-gray-700 dark:text-gray-300">{{ $subject->current_mood?->label() ?? 'Unknown' }}</span>
						</div>
					</div>

					<div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
						<h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">Negotiation Status</h3>
						<p class="text-gray-700 dark:text-gray-300">{{ $subject->status?->label() ?? 'Unknown' }}</p>
					</div>
				</div>

				<div class="mt-6">
					<h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">Negotiations</h3>
					@if($subject->negotiations->count() > 0)
						<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
							@foreach($subject->negotiations as $negotiation)
								<div class="bg-gray-50 dark:bg-gray-700 rounded-lg shadow-md p-4">
									<h4 class="font-medium text-primary-600 dark:text-primary-400">{{ $negotiation->title }}</h4>
									<p class="text-sm text-gray-500 dark:text-gray-400">
										Role: {{ $negotiation->pivot->role }}</p>
									<div class="mt-2">
										<x-button
												sm
												href="{{ route('negotiation-noc', ['negotiation' => $negotiation->title, 'tenantSubdomain' => tenant()->subdomain]) }}"
												wire:navigate>
											View
										</x-button>
									</div>
								</div>
							@endforeach
						</div>
					@else
						<p class="text-gray-500 dark:text-gray-400">No negotiations found.</p>
					@endif
				</div>
			</div>

			<div
					x-show="activeTab === 'images'"
					class="space-y-6">
				<h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">Subject Images</h3>

				@if($subject->images->isNotEmpty())
					<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
						@foreach($subject->images as $image)
							<div class="bg-gray-50 dark:bg-gray-700 rounded-lg overflow-hidden shadow-md group">
								<div class="relative aspect-w-1 aspect-h-1">
									<img
											src="{{ $image->url() }}"
											alt="{{ $image->title ?? 'Subject image' }}"
											class="w-full h-full object-cover"
									>

									@if($image->is_primary)
										<div class="absolute top-2 left-2 bg-primary-500 text-white text-xs px-2 py-1 rounded-full">
											Primary
										</div>
									@endif
								</div>

								<div class="p-3">
									@if($image->original_filename)
										<p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
											{{ $image->original_filename }}
										</p>
									@endif

									<div class="flex justify-between items-center mt-1">
										@if($image->size)
											<p class="text-xs text-gray-500 dark:text-gray-400">
												{{ round($image->size / 1024) }} KB
											</p>
										@endif

										<p class="text-xs text-gray-500 dark:text-gray-400">
											{{ $image->created_at ? $image->created_at->format('M d, Y') : '' }}
										</p>
									</div>
								</div>
							</div>
						@endforeach
					</div>
				@else
					<div class="bg-gray-50 dark:bg-gray-700 p-8 rounded-lg text-center">
						<p class="text-gray-500 dark:text-gray-400">No images available for this subject.</p>
					</div>
				@endif
			</div>
		</div>
	</div>
</div>