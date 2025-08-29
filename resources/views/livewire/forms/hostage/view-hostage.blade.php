<?php

	use App\Models\Negotiation;
	use App\Models\Hostage;
	use Livewire\Attributes\Layout;
	use Livewire\Volt\Component;
	use Illuminate\View\View;

	new #[Layout('layouts.negotiation')] class extends Component {
		public Hostage $hostage;
		public ?Negotiation $negotiation = null;

		public function mount(Hostage $hostage, ?Negotiation $negotiation = null)
		{
			$this->hostage = $hostage;
			$this->negotiation = $negotiation;

			// Load images relationship
			$this->hostage->load('images');
		}

		public function rendering(View $view):void
		{
			$view->layoutData(['negotiation' => $this->negotiation]);
		}

		public function editHostage()
		{
			return $this->redirect(route('hostage.edit', [
				'hostage' => $this->hostage,
				'negotiation' => $this->negotiation,
				'tenantSubdomain' => tenant()->subdomain
			]));
		}

		public function back()
		{
			// Redirect back to the negotiation page or dashboard
			if ($this->negotiation) {
				return redirect()->route('negotiation-noc', [
					'negotiation' => $this->negotiation->title,
					'tenantSubdomain' => tenant()->subdomain
				]);
			}

			return redirect()->route('dashboard.negotiations', tenant()->subdomain);
		}
	}

?>

<div class="py-12">
	<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
		<div class="bg-white dark:bg-dark-800 overflow-hidden shadow-sm sm:rounded-lg">
			<div class="p-6 text-gray-900 dark:text-gray-100">
				<!-- Header with breadcrumb and actions -->
				<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 border-b border-gray-200 dark:border-dark-600 pb-4">
					<div>
						<div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-2">
							<a
									href="#"
									wire:click="back"
									class="hover:text-primary-600 dark:hover:text-primary-400 transition">Dashboard</a>
							<x-icon
									name="chevron-right"
									class="size-4" />
							<span>Hostage Details</span>
						</div>
						<h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
							<x-icon
									name="user-circle"
									class="size-7 text-primary-500 dark:text-primary-400" />
							{{ $hostage->name }}
						</h2>
					</div>
					<div class="flex space-x-3">
						<x-button
								wire:click="back"
								color="secondary"
								icon="arrow-left">
							Back
						</x-button>
						<x-button
								wire:click="editHostage"
								color="primary"
								icon="pencil">
							Edit
						</x-button>
					</div>
				</div>

				<!-- Status Banner -->
				<div
						class="mb-6 p-4 rounded-lg border-l-4
					@if($hostage->status === 'Safe') border-green-500 bg-green-50 dark:bg-green-900/20 dark:border-green-600
					@elseif($hostage->status === 'Deceased') border-red-500 bg-red-50 dark:bg-red-900/20 dark:border-red-600
					@elseif($hostage->status === 'Injured') border-amber-500 bg-amber-50 dark:bg-amber-900/20 dark:border-amber-600
					@else border-blue-500 bg-blue-50 dark:bg-blue-900/20 dark:border-blue-600
					@endif">
					<div class="flex items-center gap-3">
						<x-icon
								name="information-circle"
								class="size-6
							@if($hostage->status === 'Safe') text-green-600 dark:text-green-400
							@elseif($hostage->status === 'Deceased') text-red-600 dark:text-red-400
							@elseif($hostage->status === 'Injured') text-amber-600 dark:text-amber-400
							@else text-blue-600 dark:text-blue-400
							@endif" />
						<div>
							<h3
									class="font-medium
								@if($hostage->status === 'Safe') text-green-800 dark:text-green-300
								@elseif($hostage->status === 'Deceased') text-red-800 dark:text-red-300
								@elseif($hostage->status === 'Injured') text-amber-800 dark:text-amber-300
								@else text-blue-800 dark:text-blue-300
								@endif">
								Current Status: {{ $hostage->status ?? 'Unknown' }}
							</h3>
							<p
									class="text-sm
								@if($hostage->status === 'Safe') text-green-700 dark:text-green-400
								@elseif($hostage->status === 'Deceased') text-red-700 dark:text-red-400
								@elseif($hostage->status === 'Injured') text-amber-700 dark:text-amber-400
								@else text-blue-700 dark:text-blue-400
								@endif">
								Last
								updated: {{ $hostage->updated_at ? $hostage->updated_at->format('M d, Y H:i') : 'Unknown' }}
							</p>
						</div>
					</div>
				</div>

				<!-- Main Content Grid -->
				<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
					<!-- Basic Information -->
					<x-card>
						<x-slot:header>
							<div class="flex items-center gap-2 p-2">
								<x-icon
										name="identification"
										class="size-5 text-primary-500 dark:text-primary-400" />
								<h3 class="text-lg font-semibold">Basic Information</h3>
							</div>
						</x-slot:header>

						<div class="space-y-3 p-1">
							<div class="flex border-b border-gray-100 dark:border-dark-700 pb-2">
								<span class="font-medium w-1/3 text-gray-600 dark:text-gray-300">Name:</span>
								<span class="w-2/3">{{ $hostage->name }}</span>
							</div>
							<div class="flex border-b border-gray-100 dark:border-dark-700 pb-2">
								<span class="font-medium w-1/3 text-gray-600 dark:text-gray-300">Age:</span>
								<span class="w-2/3">{{ $hostage->age ?? 'Unknown' }}</span>
							</div>
							<div class="flex border-b border-gray-100 dark:border-dark-700 pb-2">
								<span class="font-medium w-1/3 text-gray-600 dark:text-gray-300">Gender:</span>
								<span class="w-2/3">{{ $hostage->gender ?? 'Unknown' }}</span>
							</div>
							<div class="flex">
								<span class="font-medium w-1/3 text-gray-600 dark:text-gray-300">Relation:</span>
								<span class="w-2/3">{{ $hostage->relation_to_subject ?? 'Unknown' }}</span>
							</div>
						</div>
					</x-card>

					<!-- Status Information -->
					<x-card>
						<x-slot:header>
							<div class="flex items-center gap-2 p-2">
								<x-icon
										name="clipboard-document-check"
										class="size-5 text-primary-500 dark:text-primary-400" />
								<h3 class="text-lg font-semibold">Status Information</h3>
							</div>
						</x-slot:header>

						<div class="space-y-3 p-1">
							<div class="flex border-b border-gray-100 dark:border-dark-700 pb-2">
								<span class="font-medium w-1/3 text-gray-600 dark:text-gray-300">Risk Level:</span>
								<span class="w-2/3 flex items-center">
									@if($hostage->risk_level === 'High')
										<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 mr-2">
											High
										</span>
									@elseif($hostage->risk_level === 'Medium')
										<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300 mr-2">
											Medium
										</span>
									@elseif($hostage->risk_level === 'Low')
										<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 mr-2">
											Low
										</span>
									@else
										{{ $hostage->risk_level ?? 'Unknown' }}
									@endif
								</span>
							</div>
							<div class="flex border-b border-gray-100 dark:border-dark-700 pb-2">
								<span class="font-medium w-1/3 text-gray-600 dark:text-gray-300">Location:</span>
								<span class="w-2/3">{{ $hostage->location ?? 'Unknown' }}</span>
							</div>
							<div class="flex border-b border-gray-100 dark:border-dark-700 pb-2">
								<span class="font-medium w-1/3 text-gray-600 dark:text-gray-300">Injury Status:</span>
								<span class="w-2/3">{{ $hostage->injury_status ?? 'Unknown' }}</span>
							</div>
							<div class="flex">
								<span class="font-medium w-1/3 text-gray-600 dark:text-gray-300">Primary Hostage:</span>
								<span class="w-2/3">
									@if($hostage->is_primary_hostage)
										<span class="inline-flex items-center gap-1 text-green-600 dark:text-green-400">
											<x-icon
													name="check-circle"
													class="size-5" />
											Yes
										</span>
									@else
										<span class="inline-flex items-center gap-1 text-gray-500 dark:text-gray-400">
											<x-icon
													name="x-circle"
													class="size-5" />
											No
										</span>
									@endif
								</span>
							</div>
						</div>
					</x-card>

					<!-- Risk Factors -->
					<x-card>
						<x-slot:header>
							<div class="flex items-center gap-2 p-2">
								<x-icon
										name="exclamation-triangle"
										class="size-5 text-primary-500 dark:text-primary-400" />
								<h3 class="text-lg font-semibold">Risk Factors</h3>
							</div>
						</x-slot:header>

						<div class="p-1">
							@if($hostage->risk_factors && is_array($hostage->risk_factors) && count($hostage->risk_factors) > 0)
								<ul class="space-y-2">
									@foreach($hostage->risk_factors as $factor)
										<li class="flex items-start gap-2">
											<x-icon
													name="exclamation-circle"
													class="size-5 text-amber-500 dark:text-amber-400 mt-0.5" />
											<span>{{ $factor }}</span>
										</li>
									@endforeach
								</ul>
							@else
								<div class="flex items-center justify-center h-24 text-gray-500 dark:text-gray-400">
									<p>No risk factors recorded.</p>
								</div>
							@endif
						</div>
					</x-card>

					<!-- Timeline -->
					<x-card>
						<x-slot:header>
							<div class="flex items-center gap-2 p-2">
								<x-icon
										name="clock"
										class="size-5 text-primary-500 dark:text-primary-400" />
								<h3 class="text-lg font-semibold">Timeline</h3>
							</div>
						</x-slot:header>

						<div class="space-y-3 p-1">
							<div class="flex border-b border-gray-100 dark:border-dark-700 pb-2">
								<span class="font-medium w-1/3 text-gray-600 dark:text-gray-300">Last Seen:</span>
								<span class="w-2/3">{{ $hostage->last_seen_at ? $hostage->last_seen_at->format('M d, Y H:i') : 'Unknown' }}</span>
							</div>
							<div class="flex border-b border-gray-100 dark:border-dark-700 pb-2">
								<span class="font-medium w-1/3 text-gray-600 dark:text-gray-300">Freed:</span>
								<span class="w-2/3">
									@if($hostage->freed_at)
										<span class="inline-flex items-center gap-1 text-green-600 dark:text-green-400">
											<x-icon
													name="check-circle"
													class="size-5" />
											{{ $hostage->freed_at->format('M d, Y H:i') }}
										</span>
									@else
										<span class="text-gray-500 dark:text-gray-400">Not freed</span>
									@endif
								</span>
							</div>
							<div class="flex border-b border-gray-100 dark:border-dark-700 pb-2">
								<span class="font-medium w-1/3 text-gray-600 dark:text-gray-300">Created:</span>
								<span class="w-2/3">{{ $hostage->created_at ? $hostage->created_at->format('M d, Y H:i') : 'Unknown' }}</span>
							</div>
							<div class="flex">
								<span class="font-medium w-1/3 text-gray-600 dark:text-gray-300">Last Updated:</span>
								<span class="w-2/3">{{ $hostage->updated_at ? $hostage->updated_at->format('M d, Y H:i') : 'Unknown' }}</span>
							</div>
						</div>
					</x-card>
				</div>

				<!-- Images -->
				<div class="mt-8">
					<x-card>
						<x-slot:header>
							<div class="flex items-center justify-between p-2">
								<div class="flex items-center gap-2">
									<x-icon
											name="photo"
											class="size-5 text-primary-500 dark:text-primary-400" />
									<h3 class="text-lg font-semibold">Images</h3>
								</div>
								<span class="text-sm text-gray-500 dark:text-gray-400">
									{{ $hostage->images->count() }} {{ Str::plural('image', $hostage->images->count()) }}
								</span>
							</div>
						</x-slot:header>

						@if($hostage->images->count() > 0)
							<div
									x-data="{
								imageUrls: {{ json_encode($hostage->images->map->url()) }},
								currentIndex: 0,
								totalImages: {{ $hostage->images->count() }},
								
								nextImage() {
									this.currentIndex = (this.currentIndex + 1) % this.totalImages;
								},
								
								prevImage() {
									this.currentIndex = (this.currentIndex - 1 + this.totalImages) % this.totalImages;
								}
							}">
								<!-- Main Image -->
								<div class="bg-gray-100 dark:bg-dark-700 rounded-lg p-4 mb-4">
									<img
											x-bind:src="imageUrls[currentIndex]"
											alt="Hostage Image"
											class="w-full max-h-96 object-contain rounded-lg mx-auto shadow-sm"
									>

									<!-- Navigation Controls -->
									<div class="flex justify-between items-center mt-4">
										<button
												x-on:click="prevImage()"
												class="bg-white dark:bg-dark-600 hover:bg-gray-100 dark:hover:bg-dark-500 p-2 rounded-full shadow-sm transition-colors duration-200 flex items-center gap-1"
												x-bind:disabled="totalImages <= 1"
												x-bind:class="{ 'opacity-50 cursor-not-allowed': totalImages <= 1 }">
											<x-icon
													name="chevron-left"
													class="size-5" />
											<span class="sr-only">Previous</span>
										</button>

										<span
												x-text="`Image ${currentIndex + 1} of ${totalImages}`"
												class="text-sm font-medium bg-white dark:bg-dark-600 px-3 py-1 rounded-full shadow-sm"></span>

										<button
												x-on:click="nextImage()"
												class="bg-white dark:bg-dark-600 hover:bg-gray-100 dark:hover:bg-dark-500 p-2 rounded-full shadow-sm transition-colors duration-200 flex items-center gap-1"
												x-bind:disabled="totalImages <= 1"
												x-bind:class="{ 'opacity-50 cursor-not-allowed': totalImages <= 1 }">
											<x-icon
													name="chevron-right"
													class="size-5" />
											<span class="sr-only">Next</span>
										</button>
									</div>
								</div>

								<!-- Thumbnails -->
								<div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 gap-2 px-2 pb-2">
									@foreach($hostage->images as $index => $image)
										<div
												x-on:click="currentIndex = {{ $index }}"
												x-bind:class="{ 'ring-2 ring-primary-500 dark:ring-primary-400': currentIndex === {{ $index }} }"
												class="cursor-pointer rounded-lg overflow-hidden shadow-sm transition-all duration-200 hover:opacity-90 aspect-square">
											<img
													src="{{ $image->url() }}"
													alt="Hostage Image Thumbnail"
													class="w-full h-full object-cover"
											>
										</div>
									@endforeach
								</div>
							</div>
						@else
							<div class="flex flex-col items-center justify-center py-12 text-gray-500 dark:text-gray-400">
								<x-icon
										name="photograph"
										class="size-12 mb-2 opacity-50" />
								<p>No images available.</p>
							</div>
						@endif
					</x-card>
				</div>
			</div>
		</div>
	</div>
</div>