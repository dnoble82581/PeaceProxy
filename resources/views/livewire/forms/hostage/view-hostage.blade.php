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
				<div class="flex justify-between items-center">
					<h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
						Hostage Details
					</h2>
					<div class="flex space-x-2">
						<x-button
								wire:click="back"
								color="secondary">Back
						</x-button>
						<x-button
								wire:click="editHostage"
								color="primary">Edit
						</x-button>
					</div>
				</div>

				<div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
					<!-- Basic Information -->
					<div class="bg-gray-50 dark:bg-dark-700 p-4 rounded-lg">
						<h3 class="text-md font-semibold mb-4">Basic Information</h3>
						<div class="space-y-2">
							<div>
								<span class="font-medium">Name:</span>
								<span>{{ $hostage->name }}</span>
							</div>
							<div>
								<span class="font-medium">Age:</span>
								<span>{{ $hostage->age ?? 'Unknown' }}</span>
							</div>
							<div>
								<span class="font-medium">Gender:</span>
								<span>{{ $hostage->gender ?? 'Unknown' }}</span>
							</div>
							<div>
								<span class="font-medium">Relation to Subject:</span>
								<span>{{ $hostage->relation_to_subject ?? 'Unknown' }}</span>
							</div>
						</div>
					</div>

					<!-- Status Information -->
					<div class="bg-gray-50 dark:bg-dark-700 p-4 rounded-lg">
						<h3 class="text-md font-semibold mb-4">Status Information</h3>
						<div class="space-y-2">
							<div>
								<span class="font-medium">Risk Level:</span>
								<span>{{ $hostage->risk_level ?? 'Unknown' }}</span>
							</div>
							<div>
								<span class="font-medium">Location:</span>
								<span>{{ $hostage->location ?? 'Unknown' }}</span>
							</div>
							<div>
								<span class="font-medium">Status:</span>
								<span>{{ $hostage->status ?? 'Unknown' }}</span>
							</div>
							<div>
								<span class="font-medium">Injury Status:</span>
								<span>{{ $hostage->injury_status ?? 'Unknown' }}</span>
							</div>
							<div>
								<span class="font-medium">Primary Hostage:</span>
								<span>{{ $hostage->is_primary_hostage ? 'Yes' : 'No' }}</span>
							</div>
						</div>
					</div>

					<!-- Risk Factors -->
					<div class="bg-gray-50 dark:bg-dark-700 p-4 rounded-lg">
						<h3 class="text-md font-semibold mb-4">Risk Factors</h3>
						@if($hostage->risk_factors && is_array($hostage->risk_factors) && count($hostage->risk_factors) > 0)
							<ul class="list-disc list-inside">
								@foreach($hostage->risk_factors as $factor)
									<li>{{ $factor }}</li>
								@endforeach
							</ul>
						@else
							<p>No risk factors recorded.</p>
						@endif
					</div>

					<!-- Timeline -->
					<div class="bg-gray-50 dark:bg-dark-700 p-4 rounded-lg">
						<h3 class="text-md font-semibold mb-4">Timeline</h3>
						<div class="space-y-2">
							<div>
								<span class="font-medium">Last Seen:</span>
								<span>{{ $hostage->last_seen_at ? $hostage->last_seen_at->format('M d, Y H:i') : 'Unknown' }}</span>
							</div>
							<div>
								<span class="font-medium">Freed:</span>
								<span>{{ $hostage->freed_at ? $hostage->freed_at->format('M d, Y H:i') : 'Not freed' }}</span>
							</div>
							<div>
								<span class="font-medium">Created:</span>
								<span>{{ $hostage->created_at ? $hostage->created_at->format('M d, Y H:i') : 'Unknown' }}</span>
							</div>
							<div>
								<span class="font-medium">Last Updated:</span>
								<span>{{ $hostage->updated_at ? $hostage->updated_at->format('M d, Y H:i') : 'Unknown' }}</span>
							</div>
						</div>
					</div>
				</div>

				<!-- Images -->
				<div class="mt-6">
					<h3 class="text-md font-semibold mb-4">Images</h3>
					@if($hostage->images->count() > 0)
						<div
								class="grid grid-cols-2 md:grid-cols-8 gap-4"
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
							<div class="col-span-2 md:col-span-8">
								<img
										x-bind:src="imageUrls[currentIndex]"
										alt="Hostage Image"
										class="w-full max-h-96 object-contain rounded-lg mx-auto"
								>

								<!-- Navigation Controls -->
								<div class="flex justify-center items-center mt-2">
									<button
											x-on:click="prevImage()"
											class="bg-gray-200 dark:bg-dark-600 p-2 rounded-full mr-4"
											x-bind:disabled="totalImages <= 1">
										<svg
												xmlns="http://www.w3.org/2000/svg"
												class="h-5 w-5"
												viewBox="0 0 20 20"
												fill="currentColor">
											<path
													fill-rule="evenodd"
													d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
													clip-rule="evenodd" />
										</svg>
									</button>
									<span
											x-text="`${currentIndex + 1} of ${totalImages}`"
											class="text-sm"></span>
									<button
											x-on:click="nextImage()"
											class="bg-gray-200 dark:bg-dark-600 p-2 rounded-full ml-4"
											x-bind:disabled="totalImages <= 1">
										<svg
												xmlns="http://www.w3.org/2000/svg"
												class="h-5 w-5"
												viewBox="0 0 20 20"
												fill="currentColor">
											<path
													fill-rule="evenodd"
													d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
													clip-rule="evenodd" />
										</svg>
									</button>
								</div>
							</div>

							<!-- Thumbnails -->
							@foreach($hostage->images as $index => $image)
								<div
										x-on:click="currentIndex = {{ $index }}"
										x-bind:class="{ 'ring-2 ring-primary-500': currentIndex === {{ $index }} }"
										class="cursor-pointer rounded-lg overflow-hidden">
									<img
											src="{{ $image->url() }}"
											alt="Hostage Image Thumbnail"
											class="w-full h-full object-cover"
									>
								</div>
							@endforeach
						</div>
					@else
						<p>No images available.</p>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>