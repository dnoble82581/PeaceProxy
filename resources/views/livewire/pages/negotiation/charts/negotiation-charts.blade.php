<?php

	use App\DTOs\MoodLog\MoodLogDTO;
	use App\Enums\Subject\MoodLevels;
	use App\Models\Negotiation;
	use App\Models\Subject;
	use App\Services\MoodLog\MoodLogCreationService;
	use App\Services\MoodLog\MoodLogFetchingService;
	use App\Services\Negotiation\NegotiationFetchingService;
	use Livewire\Volt\Component;

	new class extends Component {
		public Subject $subject;
		public Negotiation $negotiation;
		public int $negotiationId;
		public $moodLogs = [];

		public function mount($negotiationId)
		{
			$this->negotiation = app(NegotiationFetchingService::class)->getNegotiationById($negotiationId);
			$this->subject = $this->negotiation->primarySubject();
			$this->negotiationId = $this->negotiation->id;
			$this->loadMoodLogs();
		}
		
		public function refreshChart()
		{
			$this->loadMoodLogs();
			$this->dispatch('mood-logs-updated', $this->formatMoodLogsForChart());
		}

		public function createMood(int $value):void
		{
			$dto = new MoodLogDTO(
				tenant_id: tenant()->id, subject_id: $this->subject->id, logged_by_id: authUser()->id,
				negotiation_id: $this->negotiation->id,
				mood_level: $value, context: 'Charts', meta_data: null,
				created_at: now(), updated_at: now(),
			);

			app(MoodLogCreationService::class)->createMoodLog($dto);

			// Reload mood logs for the current browser
			$this->loadMoodLogs();

			// Dispatch event to update the chart in the current browser
			$this->dispatch('mood-logs-updated', $this->formatMoodLogsForChart());

			// The chart will also be updated via the broadcast event for all other browsers
		}

		public function loadMoodLogs():void
		{
			// Get all mood logs for the subject
			$allLogs = app(MoodLogFetchingService::class)->getMoodLogsBySubject($this->subject->id);

			// Sort by created_at in descending order and take the 10 most recent
			$this->moodLogs = $allLogs->sortByDesc('created_at')->take(10)->values()->all();
		}


		public function formatMoodLogsForChart():array
		{
			// Reverse the collection to get chronological order for the chart
			$logs = collect($this->moodLogs)->reverse()->values();

			$categories = $logs->map(function ($log) {
				// Convert timestamp to user's timezone before formatting
				// Format as 'H:i' (hour:minute) for better readability
				// Use try-catch to handle potential issues with timezone conversion
				try {
					$userTimezone = authUser()->timezone ?? config('app.timezone', 'UTC');
					return $log->created_at->setTimezone($userTimezone)->format('H:i');
				} catch (Exception $e) {
					// Fallback to UTC if there's an issue with timezone conversion
					return $log->created_at->format('H:i');
				}
			})->toArray();

			$data = $logs->map(function ($log) {
				return $log->mood_level;
			})->toArray();

			return [
				'categories' => $categories,
				'data' => $data
			];
		}
	}

?>

<div
		class="rounded-lg shadow-lg p-4"
		x-data="chartComponent(@js($this->formatMoodLogsForChart()))"
		x-init="init()"
		@theme-changed.window="onThemeChanged($event.detail.theme)"
		@mood-logs-updated.window="updateChartData($event.detail)"
>
	<div class="flex justify-between items-center mb-2">
		<h3 class="text-lg font-medium">Mood Chart</h3>
		<button 
			wire:click="refreshChart"
			class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
		>
			<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
			</svg>
			Refresh Chart
		</button>
	</div>
	<div
			id="chart"
			class="w-full h-80">
	</div>
	<div class="flex items-center justify-evenly">
		@foreach(App\Enums\Subject\MoodLevels::cases() as $index => $mood)
			<div class="relative group">
				<!-- Button -->
				<button
						wire:click="createMood({{ $mood->value }})"
						class="text-3xl">
					{{ $mood->icon() }}
				</button>

				<!-- Tooltip -->
				<span
						class="absolute
						{{ $loop->first ? 'translate-x-2 left-0':($loop->last || $loop->iteration > count(App\Enums\Subject\MoodLevels::cases()) - 3 ? '-translate-x-4 right-0' : '-translate-x-1/2 left-1/2') }}
                    bottom-full mb-2 opacity-0 group-hover:opacity-100 bg-gray-900 text-white text-xs rounded-md px-2 py-1 transition duration-150 ease-in-out z-10 whitespace-nowrap">
                {{ $mood->description() }}
            </span>

			</div>
		@endforeach
	</div>
</div>

@push('scripts')
	<!-- CDN Fallback for ApexCharts in case the npm package doesn't load properly -->
	<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
	
	<style>
		/* Custom styling for ApexCharts dropdown menu in dark mode */
		.dark .apexcharts-menu {
			background-color: #1F2937 !important; /* dark-800 */
			border-color: #374151 !important; /* dark-700 */
		}
		
		.dark .apexcharts-menu-item {
			color: #F9FAFB !important; /* gray-50 */
		}
		
		.dark .apexcharts-menu-item:hover {
			background-color: #374151 !important; /* dark-700 */
		}
	</style>

	<script>
		function chartComponent (moodLogsData = { categories: [], data: [] }) {
			return {
				// Detect the initial theme (from .dark class on <html>)
				dark: document.documentElement.classList.contains('dark'),

				chart: null, // ApexCharts instance

				// Store the mood logs data
				moodLogsData: moodLogsData,

				init () {
					// Render the chart for the first time
					this.renderChart()

					// Listen for Tallstack's theme changes and update the chart dynamically
					window.addEventListener('theme-changed', (event) => {
						this.onThemeChanged(event.detail.theme)
					})
				},

				renderChart () {
					// Define fixed and fallback axis label colors for both light and dark modes
					const labelColorLight = '#4B5563' // Tailwind's gray-700 or close equivalent
					const labelColorDark = '#D1D5DB'  // Tailwind's gray-300 or close equivalent

					// Use dark or light mode colors based on the initial theme
					const labelColor = this.dark ? labelColorDark : labelColorLight

					// Default empty data if no mood logs exist yet
					const categories = this.moodLogsData.categories.length > 0
						? this.moodLogsData.categories
						: ['No data yet']

					const data = this.moodLogsData.data.length > 0
						? this.moodLogsData.data
						: [0]

					this.chart = new ApexCharts(document.querySelector('#chart'), {
						chart: {
							type: 'line',
							height: 350,
							background: 'transparent',
							toolbar: { 
								show: true // Keeps the top right menu intact
							},
							animations: {
								enabled: true,
								easing: 'easeinout',
								speed: 800,
							},
							foreColor: this.dark ? '#D1D5DB' : '#4B5563', // Dynamic text color for all chart elements
						},
						theme: {
							mode: this.dark ? 'dark' : 'light', // Set chart theme
						},
						grid: {
							borderColor: this.dark ? '#374151' : '#E5E7EB', // Slightly darker/lighter grid lines
						},
						xaxis: {
							categories: categories,
							labels: {
								style: {
									colors: labelColor, // Dynamic labels
								},
								rotate: -45,
								rotateAlways: false,
							},
							title: {
								text: 'Time',
								style: {
									color: labelColor,
								},
							},
						},
						yaxis: {
							labels: {
								style: {
									colors: labelColor, // Dynamic labels
								},
							},
							title: {
								text: 'Mood Level',
								style: {
									color: labelColor,
								},
							},
							min: 1,
							max: 11,
							tickAmount: 11,
						},
						series: [
							{
								name: 'Mood Level',
								data: data,
							},
						],
						markers: {
							size: 5,
						},
						tooltip: {
							enabled: true,
							theme: this.dark ? 'dark' : 'light', // Match tooltip theme to current mode
							style: {
								fontSize: '12px',
								fontFamily: 'inherit',
							},
							// Custom background and text colors based on theme
							custom: function({ series, seriesIndex, dataPointIndex, w }) {
								const isDark = w.config.tooltip.theme === 'dark';
								// Define colors based on theme
								const bgColor = isDark ? '#374151' : '#F3F4F6'; // Dark: gray-700, Light: gray-100
								const textColor = isDark ? '#F9FAFB' : '#1F2937'; // Dark: gray-50, Light: gray-800
								const borderColor = isDark ? '#4B5563' : '#D1D5DB'; // Dark: gray-600, Light: gray-300
								
								// Get the value and corresponding mood label
								const value = series[seriesIndex][dataPointIndex];
								const moodLabels = {
									1: 'Suicidal',
									2: 'Severely Depressed',
									3: 'Depressed',
									4: 'Sad',
									5: 'Low',
									6: 'Neutral',
									7: 'Slightly Happy',
									8: 'Happy',
									9: 'Euphoric',
									10: 'Hypomanic',
									11: 'Manic'
								};
								const moodLabel = moodLabels[value] || '';
								
								// Return custom HTML for tooltip
								return '<div class="custom-tooltip" style="' +
									'background: ' + bgColor + '; ' +
									'color: ' + textColor + '; ' +
									'border: 1px solid ' + borderColor + '; ' +
									'padding: 8px; ' +
									'border-radius: 4px; ' +
									'box-shadow: 0 2px 5px rgba(0,0,0,0.15);">' +
									'<span><strong>Mood Level:</strong> ' + value + ' - ' + moodLabel + '</span>' +
									'</div>';
							}
						},
					})

					// Render the chart
					this.chart.render()
				},

				onThemeChanged (theme) {
					// Update the dark mode state
					this.dark = theme === 'dark'

					// Dynamically update the chart colors when the theme changes
					this.updateChartTheme()
				},

				updateChartTheme () {
					if (!this.chart) return // Ensure the chart exists

					// Define fixed and fallback axis label colors for theme switching
					const labelColorLight = '#4B5563' // Tailwind's gray-700
					const labelColorDark = '#D1D5DB'  // Tailwind's gray-300

					// Use updated colors based on current theme
					const labelColor = this.dark ? labelColorDark : labelColorLight
					const gridLineColor = this.dark ? '#374151' : '#E5E7EB'

					// Update chart options dynamically
					this.chart.updateOptions({
						chart: {
							foreColor: this.dark ? '#D1D5DB' : '#4B5563', // Update text color for all chart elements
						},
						theme: {
							mode: this.dark ? 'dark' : 'light', // Update theme
						},
						grid: {
							borderColor: gridLineColor, // Update grid lines
						},
						tooltip: {
							theme: this.dark ? 'dark' : 'light', // Update tooltip theme
							// The custom function will automatically use the updated theme
						},
						xaxis: {
							labels: {
								style: {
									colors: labelColor, // Update label colors
								},
							},
							title: {
								style: {
									color: labelColor,
								},
							},
						},
						yaxis: {
							labels: {
								style: {
									colors: labelColor, // Update label colors
								},
							},
							title: {
								style: {
									color: labelColor,
								},
							},
						},
					})
				},

				// Update chart data when new mood logs are created
				updateChartData (newData) {
					if (!this.chart) return // Ensure the chart exists

					// Update the stored data
					this.moodLogsData = newData

					// Update the chart with new data
					this.chart.updateOptions({
						xaxis: {
							categories: newData.categories,
						},
						series: [{
							name: 'Mood Level',
							data: newData.data,
						}],
					})
				},
			}
		}
	</script>
@endpush
