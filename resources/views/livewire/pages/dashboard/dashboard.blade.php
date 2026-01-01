<?php

 use App\Models\Negotiation;
	use App\Models\User;
	use App\Models\Tenant;
	use App\Models\Log;
	use App\Models\Assessment;
	use App\Models\AssessmentTemplate;
	use App\Enums\Negotiation\NegotiationStatuses;
	use App\Enums\User\UserNegotiationRole;
	use App\Services\Auth\LogoutService;
	use Livewire\Attributes\Computed;
	use Livewire\Attributes\Layout;
	use Livewire\Volt\Component;
	use Illuminate\Support\Facades\DB;
 use Carbon\Carbon;
 	use Illuminate\Support\Facades\Schema;

 new #[Layout('components.layouts.app')] class extends Component {
		public $stats;
		public $negotiationStatusData;
		public $negotiationTrendsData;
		public $userRolesData;
		public $userActivityData;
		public $tenantInfo;
		public $recentLogs;
		public $fbiAssessmentData;
		public $subjectGenderData;
		public $subjectAgeData;
		public $subjectRaceData;

		// Filter properties
		public $selectedUser = null;
		public $selectedEvent = null;
		public $selectedSeverity = null;
		public $dateFrom = null;
		public $dateTo = null;

		// Filter options
		public $userOptions = [];
		public $eventOptions = [];
		public $severityOptions = [];

		public function mount()
		{
			// Get tenant ID once
			$tenantId = tenant()->id;

			// Get tenant information
			$tenant = Tenant::find($tenantId);
			$this->tenantInfo = [
				'name' => $tenant->agency_name ?? 'Unknown',
				'created_at' => $tenant->created_at? $tenant->created_at->format('M d, Y') : 'Unknown',
				'users_count' => $tenant->users()->count(),
				'negotiations_count' => $tenant->negotiations()->count(),
			];

			// Get all user stats in a single query
			$userStats = User::selectRaw('COUNT(*) as total, SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active')
				->where('tenant_id', $tenantId)
				->first();

			// Get negotiation stats in a single query
			$negotiationStats = Negotiation::selectRaw('
 			COUNT(*) as total, 
 			COUNT(CASE WHEN status = "active" THEN 1 END) as active,
 			COUNT(CASE WHEN status = "resolved" THEN 1 END) as resolved,
 			COUNT(CASE WHEN status = "failed" THEN 1 END) as failed,
 			COUNT(CASE WHEN status = "standby" THEN 1 END) as standby,
 			CASE WHEN COUNT(*) > 0 THEN SUM(duration_minutes) / COUNT(*) ELSE 0 END as avg_duration
 		')
				->where('tenant_id', $tenantId)
				->first();

			// Get negotiation trends (last 6 months)
			$sixMonthsAgo = Carbon::now()->subMonths(6);
			$negotiationTrends = Negotiation::where('tenant_id', $tenantId)
				->where('created_at', '>=', $sixMonthsAgo)
				->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
				->groupBy('month')
				->orderBy('month')
				->get()
				->pluck('count', 'month')
				->toArray();

			// Fill in missing months with zero counts
			$months = [];
			$counts = [];
			for ($i = 0; $i < 6; $i++) {
				$month = Carbon::now()->subMonths(5 - $i)->format('Y-m');
				$months[] = Carbon::now()->subMonths(5 - $i)->format('M Y');
				$counts[] = $negotiationTrends[$month] ?? 0;
			}

			$this->negotiationTrendsData = [
				'months' => $months,
				'counts' => $counts
			];

			// Get user roles distribution
			$userRoles = DB::table('negotiation_users')
				->join('negotiations', 'negotiation_users.negotiation_id', '=', 'negotiations.id')
				->where('negotiations.tenant_id', $tenantId)
				->selectRaw('role, COUNT(*) as count')
				->groupBy('role')
				->get();

			$roleLabels = [];
			$roleCounts = [];

			foreach ($userRoles as $role) {
				try {
					$enumRole = UserNegotiationRole::from($role->role);
					$roleLabels[] = $enumRole->label();
					$roleCounts[] = $role->count;
				} catch (\ValueError $e) {
					// Skip invalid roles
					continue;
				}
			}

			$this->userRolesData = [
				'labels' => $roleLabels,
				'counts' => $roleCounts
			];

			// Get user registration trends (last 6 months)
			$sixMonthsAgo = Carbon::now()->subMonths(6);
			$userActivityTrends = User::where('tenant_id', $tenantId)
				->where('created_at', '>=', $sixMonthsAgo)
				->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
				->groupBy('month')
				->orderBy('month')
				->get()
				->pluck('count', 'month')
				->toArray();

			// Fill in missing months with zero counts
			$userMonths = [];
			$userCounts = [];
			for ($i = 0; $i < 6; $i++) {
				$month = Carbon::now()->subMonths(5 - $i)->format('Y-m');
				$userMonths[] = Carbon::now()->subMonths(5 - $i)->format('M Y');
				$userCounts[] = $userActivityTrends[$month] ?? 0;
			}

			$this->userActivityData = [
				'months' => $userMonths,
				'counts' => $userCounts
			];

			// FBI High Risk Assessment distribution (tenant-wide)
			$fbiTemplateId = AssessmentTemplate::where('tenant_id', $tenantId)
				->where('name', 'FBI High Risk Assessment')
				->value('id');

			$low = 0; $moderate = 0; $high = 0; $totalAssessments = 0; $avgScore = 0;
			if ($fbiTemplateId) {
				$agg = Assessment::where('tenant_id', $tenantId)
					->where('assessment_template_id', $fbiTemplateId)
					->whereNotNull('completed_at')
					->selectRaw('SUM(CASE WHEN score BETWEEN 0 AND 3 THEN 1 ELSE 0 END) as low')
					->selectRaw('SUM(CASE WHEN score BETWEEN 4 AND 6 THEN 1 ELSE 0 END) as moderate')
					->selectRaw('SUM(CASE WHEN score BETWEEN 7 AND 10 THEN 1 ELSE 0 END) as high')
					->selectRaw('COUNT(*) as total')
					->selectRaw('COALESCE(AVG(score),0) as avg_score')
					->first();

				$low = (int) ($agg->low ?? 0);
				$moderate = (int) ($agg->moderate ?? 0);
				$high = (int) ($agg->high ?? 0);
				$totalAssessments = (int) ($agg->total ?? 0);
				$avgScore = (float) ($agg->avg_score ?? 0);
			}

			$this->fbiAssessmentData = [
				'labels' => ['Low (0–3)', 'Moderate (4–6)', 'High (7–10)'],
				'counts' => [$low, $moderate, $high],
				'colors' => ['#10B981', '#F59E0B', '#EF4444'], // emerald, amber, red
				'total' => $totalAssessments,
				'avgScore' => round($avgScore, 1),
			];

			// Subject demographics across all tenant negotiations
			$selectColumns = ['subjects.id', 'subjects.gender', 'subjects.date_of_birth'];
			$hasRaceColumn = Schema::hasColumn('subjects', 'race');
			if ($hasRaceColumn) {
				$selectColumns[] = 'subjects.race';
			}
			$subjects = DB::table('subjects')
				->join('negotiation_subjects', 'subjects.id', '=', 'negotiation_subjects.subject_id')
				->join('negotiations', 'negotiation_subjects.negotiation_id', '=', 'negotiations.id')
				->where('negotiations.tenant_id', $tenantId)
				->select($selectColumns)
				->distinct()
				->get();

			// Gender breakdown
			$genderCounts = [];
			foreach ($subjects as $s) {
				$raw = is_string($s->gender) ? trim(strtolower($s->gender)) : null;
				$label = $raw ? ucfirst($raw) : 'Unknown';
				$genderCounts[$label] = ($genderCounts[$label] ?? 0) + 1;
			}
			if (empty($genderCounts)) {
				$genderLabels = [];
				$genderSeries = [];
			} else {
				ksort($genderCounts);
				$genderLabels = array_keys($genderCounts);
				$genderSeries = array_values($genderCounts);
			}
			$this->subjectGenderData = [
				'labels' => $genderLabels,
				'counts' => $genderSeries,
				'colors' => ['#3B82F6','#10B981','#F59E0B','#EF4444','#8B5CF6','#EC4899','#6366F1','#14B8A6'],
			];

			// Age breakdown (buckets)
			$ageBuckets = [
				'<18' => 0,
				'18–24' => 0,
				'25–34' => 0,
				'35–44' => 0,
				'45–54' => 0,
				'55–64' => 0,
				'65+' => 0,
				'Unknown' => 0,
			];
			foreach ($subjects as $s) {
				if (!empty($s->date_of_birth)) {
					try {
						$age = Carbon::parse($s->date_of_birth)->age;
					} catch (\Throwable $e) {
						$age = null;
					}
				} else {
					$age = null;
				}
				if ($age === null) {
					$ageBuckets['Unknown']++;
				} elseif ($age < 18) {
					$ageBuckets['<18']++;
				} elseif ($age <= 24) {
					$ageBuckets['18–24']++;
				} elseif ($age <= 34) {
					$ageBuckets['25–34']++;
				} elseif ($age <= 44) {
					$ageBuckets['35–44']++;
				} elseif ($age <= 54) {
					$ageBuckets['45–54']++;
				} elseif ($age <= 64) {
					$ageBuckets['55–64']++;
				} else {
					$ageBuckets['65+']++;
				}
			}
			$this->subjectAgeData = [
				'labels' => array_keys($ageBuckets),
				'counts' => array_values($ageBuckets),
				'colors' => ['#60A5FA'],
			];

			// Race breakdown (if column exists)
			$raceLabels = [];
			$raceCounts = [];
			if (Schema::hasColumn('subjects', 'race')) {
				$raceMap = [];
				foreach ($subjects as $s) {
					$raw = isset($s->race) && is_string($s->race) ? trim($s->race) : '';
					$label = $raw !== '' ? $raw : 'Unknown';
					$raceMap[$label] = ($raceMap[$label] ?? 0) + 1;
				}
				ksort($raceMap);
				$raceLabels = array_keys($raceMap);
				$raceCounts = array_values($raceMap);
			}
			$this->subjectRaceData = [
				'labels' => $raceLabels,
				'counts' => $raceCounts,
				'colors' => ['#F59E0B','#10B981','#3B82F6','#EF4444','#8B5CF6','#EC4899','#6366F1','#14B8A6'],
			];

			// Prepare negotiation status data for chart
			$this->negotiationStatusData = [
				'labels' => ['Active', 'Resolved', 'Failed', 'Standby'],
				'counts' => [
					$negotiationStats->active ?? 0,
					$negotiationStats->resolved ?? 0,
					$negotiationStats->failed ?? 0,
					$negotiationStats->standby ?? 0
				],
				'colors' => [
					NegotiationStatuses::active->color(),
					NegotiationStatuses::resolved->color(),
					NegotiationStatuses::failed->color(),
					NegotiationStatuses::standby->color()
				]
			];

			// Store all stats in a single property
			$this->stats = [
				'users' => [
					'total' => $userStats->total ?? 0,
					'active' => $userStats->active ?? 0,
					'inactive' => ($userStats->total ?? 0) - ($userStats->active ?? 0),
				],
				'negotiations' => [
					'total' => $negotiationStats->total ?? 0,
					'active' => $negotiationStats->active ?? 0,
					'resolved' => $negotiationStats->resolved ?? 0,
					'failed' => $negotiationStats->failed ?? 0,
					'standby' => $negotiationStats->standby ?? 0,
					'avgDuration' => round($negotiationStats->avg_duration ?? 0),
				],
			];

			// Initialize filter options
			$this->initializeFilterOptions($tenantId);

			// Load logs with default filters
			$this->loadLogs();
		}

		/**
		 * Initialize filter options for activity logs
		 */
		public function initializeFilterOptions($tenantId)
		{
			// Get unique users who have activity logs
			$users = DB::table('logs')
				->join('users', function ($join) {
					$join->on('logs.actor_id', '=', 'users.id')
						->where('logs.actor_type', '=', 'App\\Models\\User');
				})
				->where('logs.tenant_id', $tenantId)
				->select('users.id', 'users.name')
				->distinct()
				->orderBy('users.name')
				->get();

			$this->userOptions = $users->map(function ($user) {
				return [
					'id' => $user->id,
					'name' => $user->name
				];
			})->toArray();

			// Get unique event types
			$events = DB::table('logs')
				->where('tenant_id', $tenantId)
				->select('event')
				->distinct()
				->orderBy('event')
				->pluck('event')
				->toArray();

			$this->eventOptions = $events;

			// Get unique severity levels
			$severities = DB::table('logs')
				->where('tenant_id', $tenantId)
				->select('severity')
				->distinct()
				->orderBy('severity')
				->pluck('severity')
				->toArray();

			$this->severityOptions = $severities;
		}

		/**
		 * Load logs with applied filters
		 */
		public function loadLogs()
		{
			$query = Log::forTenant(tenant()->id)
				->orderBy('occurred_at', 'desc');

			// Apply user filter
			if ($this->selectedUser) {
				$query->where('actor_type', 'App\\Models\\User')
					->where('actor_id', $this->selectedUser);
			}

			// Apply event filter
			if ($this->selectedEvent) {
				$query->where('event', $this->selectedEvent);
			}

			// Apply severity filter
			if ($this->selectedSeverity) {
				$query->where('severity', $this->selectedSeverity);
			}

			// Apply date range filter
			if ($this->dateFrom && $this->dateTo) {
				$from = Carbon::parse($this->dateFrom)->startOfDay();
				$to = Carbon::parse($this->dateTo)->endOfDay();
				$query->whereBetween('occurred_at', [$from, $to]);
			} elseif ($this->dateFrom) {
				$from = Carbon::parse($this->dateFrom)->startOfDay();
				$query->where('occurred_at', '>=', $from);
			} elseif ($this->dateTo) {
				$to = Carbon::parse($this->dateTo)->endOfDay();
				$query->where('occurred_at', '<=', $to);
			}

			// Get the logs
			$this->recentLogs = $query->limit(10)->get();
		}

		/**
		 * Reset all filters and reload logs
		 */
		public function resetFilters()
		{
			$this->selectedUser = null;
			$this->selectedEvent = null;
			$this->selectedSeverity = null;
			$this->dateFrom = null;
			$this->dateTo = null;

			$this->loadLogs();
		}
	}

?>
<div
		x-data="dashboardCharts()"
		x-init="init()"
		@theme-changed.window="onThemeChanged($event.detail.theme)"
		class="p-4 dark:text-white">
	<!-- 
	Manual theme toggle button (fallback if automatic detection fails)
	This button allows users to manually toggle between light and dark mode
	if the automatic theme detection with TallStack UI isn't working properly.
	It directly toggles the 'dark' class on the HTML element and dispatches
	the appropriate events to update all components.
	-->


	<!-- 
	Alpine.js component script for dashboard charts
	This component handles the initialization and updating of all charts on the dashboard.
	It includes special handling for dark mode to ensure chart text is visible in both themes.
	A manual theme toggle button is provided as a fallback if automatic detection fails.
	-->
	<script>
		function dashboardCharts () {
			return {
				// Chart instances that will be initialized
				statusChart: null,  // Donut chart for negotiation status distribution
				trendsChart: null,  // Area chart for negotiation trends
				rolesChart: null,   // Bar chart for user roles
				activityChart: null, // Area chart for user activity/growth
				fbiAssessmentChart: null, // Donut chart for FBI High Risk Assessment distribution
				subjectGenderChart: null, // Donut chart for subject gender breakdown
				subjectAgeChart: null, // Bar chart for subject age buckets
				subjectRaceChart: null, // Donut chart for subject race breakdown

				// Track whether charts have been initialized
				chartsInitialized: false,

				// Track dark mode state - initialized from HTML element class
				dark: document.documentElement.classList.contains('dark'),

				// Initialize the component
				init () {
					// Listen for ApexCharts loaded event
					window.addEventListener('apexcharts-loaded', () => {
						this.initCharts()
					})

					// Try to initialize immediately if ApexCharts is already available
					if (typeof ApexCharts !== 'undefined') {
						this.initCharts()
					}

					// We're using the Alpine directive @theme-changed.window for theme changes
					// No need for an additional event listener here
				},

				// Manual theme toggle function (fallback if automatic detection fails)
				manualThemeToggle () {
					// Toggle the dark class on the HTML element
					const html = document.documentElement
					html.classList.toggle('dark')

					// Update the dark mode state
					this.dark = html.classList.contains('dark')
					console.log('Manual theme toggle - Dark mode:', this.dark)

					// Dispatch a theme-changed event to ensure all components are updated
					window.dispatchEvent(new CustomEvent('theme-changed', {
						detail: { theme: this.dark ? 'dark' : 'light' }
					}))

					// Reinitialize the charts to ensure they reflect the new theme
					if (this.chartsInitialized) {
						console.log('Reinitializing charts after manual theme toggle')
						this.initCharts()
					}
				},

				// Handle theme changes
				onThemeChanged (theme) {
					console.log('Theme changed event received:', theme)

					// Update the dark mode state
					this.dark = theme === 'dark'
					console.log('Dark mode state updated:', this.dark)

					// For more reliable theme changes, completely reinitialize the charts
					// This ensures all chart elements are properly updated with the new colors
					if (this.chartsInitialized) {
						console.log('Reinitializing charts for theme change')
						this.initCharts()
					}
				},

				// Update chart themes without reinitializing
				updateChartTheme () {
					// Define fixed colors for both light and dark modes
					const labelColorLight = '#4B5563' // Tailwind's gray-700
					const labelColorDark = '#FFFFFF'  // Pure white for maximum visibility in dark mode
					const gridLineColor = this.dark ? '#374151' : '#E5E7EB'

					// Use updated colors based on current theme
					const labelColor = this.dark ? labelColorDark : labelColorLight

					// Debug logging to verify theme detection
					console.log('Theme changed - Dark mode:', this.dark)

					// Update all charts if they exist
					if (this.statusChart) {
						this.statusChart.updateOptions({
							chart: {
								foreColor: this.dark ? '#D1D5DB' : '#4B5563',
							},
							theme: {
								mode: this.dark ? 'dark' : 'light',
							},
							tooltip: {
								theme: this.dark ? 'dark' : 'light',
							}
						})
					}

					if (this.trendsChart) {
						this.trendsChart.updateOptions({
							chart: {
								foreColor: this.dark ? '#D1D5DB' : '#4B5563',
							},
							theme: {
								mode: this.dark ? 'dark' : 'light',
							},
							grid: {
								borderColor: gridLineColor,
							},
							xaxis: {
								labels: {
									style: {
										colors: labelColor,
									}
								}
							},
							yaxis: {
								labels: {
									style: {
										colors: labelColor,
									}
								}
							},
							tooltip: {
								theme: this.dark ? 'dark' : 'light',
							}
						})
					}

					if (this.rolesChart) {
						this.rolesChart.updateOptions({
							chart: {
								foreColor: this.dark ? '#D1D5DB' : '#4B5563',
							},
							theme: {
								mode: this.dark ? 'dark' : 'light',
							},
							grid: {
								borderColor: gridLineColor,
							},
							xaxis: {
								labels: {
									style: {
										colors: labelColor,
									}
								}
							},
							yaxis: {
								labels: {
									style: {
										colors: labelColor,
									}
								}
							},
							tooltip: {
								theme: this.dark ? 'dark' : 'light',
							}
						})
					}

					if (this.activityChart) {
						this.activityChart.updateOptions({
							chart: {
								foreColor: this.dark ? '#D1D5DB' : '#4B5563',
							},
							theme: {
								mode: this.dark ? 'dark' : 'light',
							},
							grid: {
								borderColor: gridLineColor,
							},
							xaxis: {
								labels: {
									style: {
										colors: labelColor,
									}
								}
							},
							yaxis: {
								labels: {
									style: {
										colors: labelColor,
									}
								}
							},
							tooltip: {
								theme: this.dark ? 'dark' : 'light',
  					}
  				})
  			}

  			if (this.fbiAssessmentChart) {
  				this.fbiAssessmentChart.updateOptions({
  					chart: {
  						foreColor: this.dark ? '#D1D5DB' : '#4B5563',
  					},
  					theme: {
  						mode: this.dark ? 'dark' : 'light',
  					},
  					tooltip: {
  						theme: this.dark ? 'dark' : 'light',
  					}
  				})
  			}

  			if (this.subjectGenderChart) {
  				this.subjectGenderChart.updateOptions({
  					chart: { foreColor: this.dark ? '#D1D5DB' : '#4B5563' },
  					theme: { mode: this.dark ? 'dark' : 'light' },
  					tooltip: { theme: this.dark ? 'dark' : 'light' },
  				})
  			}
  			if (this.subjectAgeChart) {
  				const gridLineColor = this.dark ? '#374151' : '#E5E7EB'
  				const labelColor = this.dark ? '#FFFFFF' : '#4B5563'
  				this.subjectAgeChart.updateOptions({
  					chart: { foreColor: this.dark ? '#D1D5DB' : '#4B5563' },
  					theme: { mode: this.dark ? 'dark' : 'light' },
  					grid: { borderColor: gridLineColor },
  					xaxis: { labels: { style: { colors: labelColor } } },
  					yaxis: { labels: { style: { colors: labelColor } } },
  					tooltip: { theme: this.dark ? 'dark' : 'light' },
  				})
  			}
  			if (this.subjectRaceChart) {
  				this.subjectRaceChart.updateOptions({
  					chart: { foreColor: this.dark ? '#D1D5DB' : '#4B5563' },
  					theme: { mode: this.dark ? 'dark' : 'light' },
  					tooltip: { theme: this.dark ? 'dark' : 'light' },
  				})
  			}
  		},

				// Main initialization function
				initCharts () {
					// Check if ApexCharts is available
					if (typeof ApexCharts === 'undefined') {
						console.warn('ApexCharts not available yet. Charts will be initialized when loaded.')
						return
					}

					// Clean up existing charts if they exist
					this.cleanupCharts()

					// Initialize all charts
					this.initNegotiationStatusChart()
					this.initNegotiationTrendsChart()
					this.initUserRolesChart()
					this.initUserActivityChart()
					this.initFbiAssessmentChart()
					this.initSubjectGenderChart()
					this.initSubjectAgeChart()
					this.initSubjectRaceChart()
					
					this.chartsInitialized = true
				},

				// Clean up existing charts
				cleanupCharts () {
					if (this.statusChart) {
						this.statusChart.destroy()
						this.statusChart = null
					}

					if (this.trendsChart) {
						this.trendsChart.destroy()
						this.trendsChart = null
					}

					if (this.rolesChart) {
						this.rolesChart.destroy()
						this.rolesChart = null
					}

					if (this.activityChart) {
						this.activityChart.destroy()
						this.activityChart = null
					}
					if (this.fbiAssessmentChart) {
						this.fbiAssessmentChart.destroy()
						this.fbiAssessmentChart = null
					}
					if (this.subjectGenderChart) {
						this.subjectGenderChart.destroy()
						this.subjectGenderChart = null
					}
					if (this.subjectAgeChart) {
						this.subjectAgeChart.destroy()
						this.subjectAgeChart = null
					}
					if (this.subjectRaceChart) {
						this.subjectRaceChart.destroy()
						this.subjectRaceChart = null
					}
				},

				// Show empty state for a chart
				showEmptyState (elementId, message) {
					const element = document.querySelector(elementId)
					if (element) {
						// Create the empty state element
						const emptyState = document.createElement('div')
						emptyState.className = 'chart-empty-state'
						emptyState.textContent = message

						// Clear the element and append the empty state
						element.innerHTML = ''
						element.appendChild(emptyState)
					}
				},

				// Initialize FBI High Risk Assessment chart
				initFbiAssessmentChart () {
					const data = @json($fbiAssessmentData['counts']);
					const labels = @json($fbiAssessmentData['labels']);
					const colors = @json($fbiAssessmentData['colors']);
					const total = @json($fbiAssessmentData['total']);
					const avgScore = @json($fbiAssessmentData['avgScore']);

					const container = document.querySelector('#fbi-assessment-chart')
					if (!container) {
						return
					}

					if (!data || data.reduce((a, b) => a + b, 0) === 0) {
						this.showEmptyState('#fbi-assessment-chart', 'No completed FBI assessments yet')
						return
					}

					const options = {
						series: data,
						labels: labels,
						chart: {
							type: 'donut',
							height: 320,
							fontFamily: 'Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto',
							foreColor: this.dark ? '#D1D5DB' : '#4B5563',
						},
						colors: colors,
						plotOptions: {
							pie: {
								donut: {
									size: '68%',
									labels: {
										show: true,
										total: {
											show: true,
											label: 'Completed',
											fontSize: '14px',
											color: this.dark ? '#D1D5DB' : '#4B5563',
											formatter: () => `${total} | Avg ${avgScore}`,
										}
									}
								}
							}
						},
						legend: {
							position: 'bottom',
							labels: {
								colors: this.dark ? '#D1D5DB' : '#4B5563',
							}
						},
						dataLabels: {
							enabled: true,
							formatter: (val) => `${Math.round(val)}%`,
							style: {
								colors: [this.dark ? '#F9FAFB' : '#111827']
							}
						},
						tooltip: {
							theme: this.dark ? 'dark' : 'light',
							fillSeriesColor: false,
							y: {
								formatter: (value) => `${value} assessments`
							}
						}
					}

					this.fbiAssessmentChart = new ApexCharts(document.querySelector('#fbi-assessment-chart'), options)
					this.fbiAssessmentChart.render()
				
					// Return early to keep method boundaries clear
					return
				},

				// Initialize Subject Gender chart
				initSubjectGenderChart () {
					const data = @json($subjectGenderData['counts'] ?? []);
					const labels = @json($subjectGenderData['labels'] ?? []);
					const colors = @json($subjectGenderData['colors'] ?? []);

					const el = document.querySelector('#subject-gender-chart')
					if (!el) { return }
					if (!data.length || !data.some(v => v > 0)) {
						this.showEmptyState('#subject-gender-chart', 'No subject gender data')
						return
					}

					const options = {
						series: data,
						labels: labels,
						chart: {
							type: 'donut',
							height: 280,
							foreColor: this.dark ? '#D1D5DB' : '#4B5563',
						},
						colors: colors.length ? colors : ['#3B82F6','#10B981','#F59E0B','#EF4444','#8B5CF6'],
						legend: { position: 'bottom' },
						dataLabels: { enabled: true, formatter: (val) => `${Math.round(val)}%` },
						tooltip: { theme: this.dark ? 'dark' : 'light' },
						plotOptions: { pie: { donut: { size: '60%' } } },
					}
					this.subjectGenderChart = new ApexCharts(el, options)
					this.subjectGenderChart.render()
				},

				// Initialize Subject Age chart
				initSubjectAgeChart () {
					const data = @json($subjectAgeData['counts'] ?? []);
					const labels = @json($subjectAgeData['labels'] ?? []);

					const el = document.querySelector('#subject-age-chart')
					if (!el) { return }
					if (!data.length || !data.some(v => v > 0)) {
						this.showEmptyState('#subject-age-chart', 'No subject age data')
						return
					}

					const labelColor = this.dark ? '#FFFFFF' : '#4B5563'
					const gridLineColor = this.dark ? '#374151' : '#E5E7EB'
					const options = {
						series: [{ name: 'Subjects', data }],
						chart: { type: 'bar', height: 280, foreColor: this.dark ? '#D1D5DB' : '#4B5563', toolbar: { show: false } },
						plotOptions: { bar: { horizontal: true, borderRadius: 4 } },
						dataLabels: { enabled: false },
						grid: { borderColor: gridLineColor },
						xaxis: { categories: labels, labels: { style: { colors: labelColor } } },
						yaxis: { labels: { style: { colors: labelColor } } },
						colors: ['#60A5FA'],
						tooltip: { theme: this.dark ? 'dark' : 'light' },
					}
					this.subjectAgeChart = new ApexCharts(el, options)
					this.subjectAgeChart.render()
				},

				// Initialize Subject Race chart
				initSubjectRaceChart () {
					const data = @json($subjectRaceData['counts'] ?? []);
					const labels = @json($subjectRaceData['labels'] ?? []);
					const colors = @json($subjectRaceData['colors'] ?? []);

					const el = document.querySelector('#subject-race-chart')
					if (!el) { return }
					if (!labels.length || !data.length || !data.some(v => v > 0)) {
						this.showEmptyState('#subject-race-chart', 'No subject race data')
						return
					}

					const options = {
						series: data,
						labels: labels,
						chart: { type: 'donut', height: 280, foreColor: this.dark ? '#D1D5DB' : '#4B5563' },
						colors: colors.length ? colors : ['#F59E0B','#10B981','#3B82F6','#EF4444','#8B5CF6'],
						legend: { position: 'bottom' },
						dataLabels: { enabled: true, formatter: (val) => `${Math.round(val)}%` },
						tooltip: { theme: this.dark ? 'dark' : 'light' },
						plotOptions: { pie: { donut: { size: '60%' } } },
					}
					this.subjectRaceChart = new ApexCharts(el, options)
					this.subjectRaceChart.render()
				},

				// Initialize negotiation status chart
				initNegotiationStatusChart () {
					const data = @json($negotiationStatusData['counts']);

					// Check if there's data to display
					if (!data.some(value => value > 0)) {
						this.showEmptyState('#negotiation-status-chart', 'No negotiation status data available')
						return
					}

					// Map color names to hex values
					const colorMap = {
						'emerald': '#10B981',
						'zinc': '#71717A',
						'red': '#EF4444',
						'blue': '#3B82F6'
					}

					// Get colors from negotiationStatusData or use defaults
					const colors = @json($negotiationStatusData['colors']).
					map(function (color) {
						return colorMap[color] || '#10B981'
					})

					// Define label colors based on theme
					const labelColorLight = '#4B5563' // Tailwind's gray-700
					const labelColorDark = '#FFFFFF'  // Pure white for maximum visibility in dark mode
					const labelColor = this.dark ? labelColorDark : labelColorLight

					const options = {
						series: data,
						chart: {
							type: 'donut',
							height: 240,
							background: 'transparent',
							foreColor: this.dark ? '#D1D5DB' : '#4B5563',
						},
						labels: @json($negotiationStatusData['labels']),
						colors: colors,
						legend: {
							position: 'bottom',
							fontSize: '14px',
						},
						dataLabels: {
							enabled: false
						},
						tooltip: {
							enabled: true,
							theme: this.dark ? 'dark' : 'light',
						},
						plotOptions: {
							pie: {
								donut: {
									size: '60%',
									labels: {
										show: true,
										total: {
											show: true,
											label: 'Total',
											formatter: function (w) {
												return w.globals.seriesTotals.reduce(function (a, b) { return a + b }, 0)
											}
										}
									}
								}
							}
						},
						responsive: [{
							breakpoint: 480,
							options: {
								chart: {
									height: 200
								},
								legend: {
									position: 'bottom'
								}
							}
						}]
					}

					this.statusChart = new ApexCharts(document.querySelector('#negotiation-status-chart'), options)
					this.statusChart.render()
				},

				// Initialize negotiation trends chart
				initNegotiationTrendsChart () {
					const data = @json($negotiationTrendsData['counts']);

					// Check if there's data to display
					if (!data.some(function (value) { return value > 0 })) {
						this.showEmptyState('#negotiation-trends-chart', 'No negotiation trend data available')
						return
					}

					// Define label colors based on theme
					const labelColorLight = '#4B5563' // Tailwind's gray-700
					const labelColorDark = '#FFFFFF'  // Pure white for maximum visibility in dark mode
					const labelColor = this.dark ? labelColorDark : labelColorLight
					const gridLineColor = this.dark ? '#374151' : '#E5E7EB'

					const options = {
						series: [{
							name: 'Negotiations',
							data: data
						}],
						chart: {
							type: 'area',
							height: 240,
							background: 'transparent',
							foreColor: this.dark ? '#D1D5DB' : '#4B5563',
							toolbar: {
								show: false
							}
						},
						dataLabels: {
							enabled: false
						},
						stroke: {
							curve: 'smooth',
							width: 3
						},
						grid: {
							borderColor: gridLineColor,
						},
						xaxis: {
							categories: @json($negotiationTrendsData['months']),
							labels: {
								style: {
									colors: labelColor,
								}
							}
						},
						yaxis: {
							labels: {
								style: {
									colors: labelColor,
								}
							}
						},
						tooltip: {
							theme: this.dark ? 'dark' : 'light',
						},
						colors: ['#10B981'], // emerald
						fill: {
							type: 'gradient',
							gradient: {
								shade: 'dark',
								type: 'vertical',
								shadeIntensity: 0.3,
								opacityFrom: 0.7,
								opacityTo: 0.2,
								stops: [0, 100]
							}
						}
					}

					this.trendsChart = new ApexCharts(document.querySelector('#negotiation-trends-chart'), options)
					this.trendsChart.render()
				},

				// Initialize user roles chart
				initUserRolesChart () {
					const labels = @json($userRolesData['labels']);
					const data = @json($userRolesData['counts']);

					// Check if there's data to display
					if (!labels.length || !data.length || !data.some(function (value) { return value > 0 })) {
						this.showEmptyState('#user-roles-chart', 'No user roles data available')
						return
					}

					// Define label colors based on theme
					const labelColorLight = '#4B5563' // Tailwind's gray-700
					const labelColorDark = '#FFFFFF'  // Pure white for maximum visibility in dark mode
					const labelColor = this.dark ? labelColorDark : labelColorLight
					const gridLineColor = this.dark ? '#374151' : '#E5E7EB'

					const options = {
						series: [{
							name: 'Users',
							data: data
						}],
						chart: {
							type: 'bar',
							height: 240,
							background: 'transparent',
							foreColor: this.dark ? '#D1D5DB' : '#4B5563',
							toolbar: {
								show: false
							}
						},
						plotOptions: {
							bar: {
								horizontal: true,
								borderRadius: 4,
								distributed: true
							}
						},
						dataLabels: {
							enabled: false
						},
						grid: {
							borderColor: gridLineColor,
						},
						xaxis: {
							categories: labels,
							labels: {
								style: {
									colors: labelColor,
								}
							}
						},
						yaxis: {
							labels: {
								style: {
									colors: labelColor,
								}
							}
						},
						tooltip: {
							theme: this.dark ? 'dark' : 'light',
						},
						colors: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#6366F1', '#14B8A6', '#F97316', '#A855F7']
					}

					this.rolesChart = new ApexCharts(document.querySelector('#user-roles-chart'), options)
					this.rolesChart.render()
				},

				// Initialize user activity chart
				initUserActivityChart () {
					const data = @json($userActivityData['counts']);

					// Check if there's data to display
					if (!data.some(function (value) { return value > 0 })) {
						this.showEmptyState('#user-activity-chart', 'No user activity data available')
						return
					}

					// Define label colors based on theme
					const labelColorLight = '#4B5563' // Tailwind's gray-700
					const labelColorDark = '#FFFFFF'  // Pure white for maximum visibility in dark mode
					const labelColor = this.dark ? labelColorDark : labelColorLight
					const gridLineColor = this.dark ? '#374151' : '#E5E7EB'

					const options = {
						series: [{
							name: 'New Users',
							data: data
						}],
						chart: {
							type: 'area',
							height: 240,
							background: 'transparent',
							foreColor: this.dark ? '#D1D5DB' : '#4B5563',
							toolbar: {
								show: false
							}
						},
						dataLabels: {
							enabled: false
						},
						stroke: {
							curve: 'smooth',
							width: 3
						},
						grid: {
							borderColor: gridLineColor,
						},
						xaxis: {
							categories: @json($userActivityData['months']),
							labels: {
								style: {
									colors: labelColor,
								}
							}
						},
						yaxis: {
							labels: {
								style: {
									colors: labelColor,
								}
							}
						},
						tooltip: {
							theme: this.dark ? 'dark' : 'light',
						},
						colors: ['#8B5CF6'], // purple
						fill: {
							type: 'gradient',
							gradient: {
								shade: 'dark',
								type: 'vertical',
								shadeIntensity: 0.3,
								opacityFrom: 0.7,
								opacityTo: 0.2,
								stops: [0, 100]
							}
						}
					}

					this.activityChart = new ApexCharts(document.querySelector('#user-activity-chart'), options)
					this.activityChart.render()
				}
			}
		}
	</script>

	<!-- Tenant Information -->
	<div class="mb-6">
		<h2 class="text-2xl font-bold mb-4">{{ $tenantInfo['name'] }} Dashboard</h2>
		<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
			<x-card class="bg-gradient-to-br from-blue-500 to-blue-600 text-white">
				<div class="p-4">
					<div class="text-sm opacity-80">Tenant Since</div>
					<div class="text-xl font-bold">{{ $tenantInfo['created_at'] }}</div>
				</div>
			</x-card>
			<x-card class="bg-gradient-to-br from-emerald-500 to-emerald-600 text-white">
				<div class="p-4">
					<div class="text-sm">Total Users</div>
					<div class="text-xl font-bold">{{ $tenantInfo['users_count'] }}</div>
				</div>
			</x-card>
			<x-card class="bg-gradient-to-br from-amber-500 to-amber-600 text-white">
				<div class="p-4">
					<div class="text-sm">Total Negotiations</div>
					<div class="text-xl font-bold">{{ $tenantInfo['negotiations_count'] }}</div>
				</div>
			</x-card>
			<x-card class="bg-gradient-to-br from-purple-500 to-purple-600 text-white">
				<div class="p-4">
					<div class="text-sm opacity-80">Avg. Negotiation Duration</div>
					<div class="text-xl font-bold">{{ $stats['negotiations']['avgDuration'] }} minutes</div>
				</div>
			</x-card>
		</div>
	</div>

	<!-- Negotiation Charts -->
	<div class="mb-6">
		<h3 class="text-xl font-bold mb-4">Negotiation Analytics</h3>
		<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
			<x-card>
				<x-slot:header>
					<h4 class="p-2 text-lg bg-gray-200/50 dark:bg-dark-800/50 rounded-t-lg">Status Distribution</h4>
				</x-slot:header>
				<div class="p-4">
					<div id="negotiation-status-chart"></div>
				</div>
			</x-card>
			<x-card>
				<x-slot:header>
					<h4 class="p-2 text-lg bg-gray-200/50 dark:bg-dark-800/50 rounded-t-lg">6-Month Trend</h4>
				</x-slot:header>
				<div class="p-4">
					<div id="negotiation-trends-chart"></div>
				</div>
			</x-card>
		</div>
	</div>

	<!-- Subject Demographics -->
	<div class="mb-6">
		<h3 class="text-xl font-bold mb-4">Subject Demographics</h3>
		<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
			<x-card>
				<x-slot:header>
					<h4 class="p-2 text-lg bg-gray-200/50 dark:bg-dark-800/50 rounded-t-lg">By Gender</h4>
				</x-slot:header>
				<div class="p-4">
					<div id="subject-gender-chart"></div>
				</div>
			</x-card>
			<x-card>
				<x-slot:header>
					<h4 class="p-2 text-lg bg-gray-200/50 dark:bg-dark-800/50 rounded-t-lg">By Age</h4>
				</x-slot:header>
				<div class="p-4">
					<div id="subject-age-chart"></div>
				</div>
			</x-card>
			<x-card>
				<x-slot:header>
					<h4 class="p-2 text-lg bg-gray-200/50 dark:bg-dark-800/50 rounded-t-lg">By Race</h4>
				</x-slot:header>
				<div class="p-4">
					<div id="subject-race-chart"></div>
				</div>
			</x-card>
		</div>
	</div>

	<!-- Assessment Analytics -->
	<div class="mb-6">
		<h3 class="text-xl font-bold mb-4">Assessment Analytics</h3>
		<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
			<x-card>
				<x-slot:header>
					<h4 class="p-2 text-lg bg-gray-200/50 dark:bg-dark-800/50 rounded-t-lg">FBI High Risk Assessment</h4>
				</x-slot:header>
				<div class="p-4">
					<div id="fbi-assessment-chart"></div>
				</div>
			</x-card>
		</div>
	</div>

	<!-- User Analytics -->
	<div class="mb-6">
		<h3 class="text-xl font-bold mb-4">User Analytics</h3>
		<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
			<x-card>
				<x-slot:header>
					<h4 class="p-2 text-lg bg-gray-200/50 dark:bg-dark-800/50 rounded-t-lg">User Status</h4>
				</x-slot:header>
				<div class="p-4">
					<div class="flex flex-col space-y-4">
						<div class="flex items-center">
							<div class="w-3 h-3 rounded-full bg-emerald-500 mr-2"></div>
							<div class="flex-1 text-sm">Active Users</div>
							<div class="font-bold">{{ $stats['users']['active'] }}</div>
						</div>
						<div class="flex items-center">
							<div class="w-3 h-3 rounded-full bg-gray-400 mr-2"></div>
							<div class="flex-1 text-sm">Inactive Users</div>
							<div class="font-bold">{{ $stats['users']['inactive'] }}</div>
						</div>
						<div class="flex items-center">
							<div class="w-3 h-3 rounded-full bg-blue-500 mr-2"></div>
							<div class="flex-1 text-sm">Total Users</div>
							<div class="font-bold">{{ $stats['users']['total'] }}</div>
						</div>
					</div>
				</div>
			</x-card>
			<x-card class="md:col-span-2">
				<x-slot:header>
					<h4 class="p-2 text-lg bg-gray-200/50 dark:bg-dark-800/50 rounded-t-lg">User Roles in
					                                                                        Negotiations</h4>
				</x-slot:header>
				<div class="p-4">
					<div id="user-roles-chart"></div>
				</div>
			</x-card>
			<x-card class="md:col-span-3">
				<x-slot:header>
					<h4 class="p-2 text-lg bg-gray-200/50 dark:bg-dark-800/50 rounded-t-lg">User Growth (6-Month
					                                                                        Trend)</h4>
				</x-slot:header>
				<div class="p-4">
					<div id="user-activity-chart"></div>
				</div>
			</x-card>
		</div>
	</div>

	<!-- Detailed Statistics -->
	<div>
		<h3 class="text-xl font-bold mb-4">Detailed Statistics</h3>
		<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
			<x-card>
				<x-slot:header>
					<h4 class="p-2 text-lg bg-gray-200/50 dark:bg-dark-800/50 rounded-t-lg">Negotiations</h4>
				</x-slot:header>
				<div class="space-y-3 bg-gray-100/60 dark:bg-dark-700 p-4 rounded-lg">
					<div class="flex justify-between text-sm border-b pb-2">
						<div>Total</div>
						<div class="font-bold">{{ $stats['negotiations']['total'] }}</div>
					</div>
					<div class="flex justify-between text-sm border-b pb-2">
						<div>Active</div>
						<div class="font-bold text-emerald-500">{{ $stats['negotiations']['active'] }}</div>
					</div>
					<div class="flex justify-between text-sm border-b pb-2">
						<div>Resolved</div>
						<div class="font-bold text-gray-500">{{ $stats['negotiations']['resolved'] }}</div>
					</div>
					<div class="flex justify-between text-sm border-b pb-2">
						<div>Failed</div>
						<div class="font-bold text-red-500">{{ $stats['negotiations']['failed'] }}</div>
					</div>
					<div class="flex justify-between text-sm border-b pb-2">
						<div>Standby</div>
						<div class="font-bold text-blue-500">{{ $stats['negotiations']['standby'] }}</div>
					</div>
					<div class="flex justify-between text-sm">
						<div>Average Duration</div>
						<div class="font-bold">{{ $stats['negotiations']['avgDuration'] }} minutes</div>
					</div>
				</div>
			</x-card>
			<x-card>
				<x-slot:header>
					<h4 class="p-2 text-lg bg-gray-200/50 dark:bg-dark-800/50 rounded-t-lg">Recent Activity</h4>
				</x-slot:header>

				<!-- Filter Toggle Button -->
				<div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-dark-800 border-b border-gray-200 dark:border-gray-700">
					<div class="flex items-center">
						<span class="text-sm font-medium text-gray-700 dark:text-gray-300">Filters</span>
						@if($selectedUser || $selectedEvent || $selectedSeverity || $dateFrom || $dateTo)
							<div class="ml-2 flex flex-wrap gap-1">
								@if($selectedUser)
									@php
										$userName = collect($userOptions)->firstWhere('id', $selectedUser)['name'] ?? 'Unknown';
									@endphp
									<span class="inline-flex items-center rounded-md bg-blue-50 dark:bg-blue-900/30 px-1.5 py-0.5 text-xs font-medium text-blue-700 dark:text-blue-300 ring-1 ring-inset ring-blue-700/10 dark:ring-blue-600/30">
										{{ $userName }}
									</span>
								@endif

								@if($selectedEvent)
									<span class="inline-flex items-center rounded-md bg-green-50 dark:bg-green-900/30 px-1.5 py-0.5 text-xs font-medium text-green-700 dark:text-green-300 ring-1 ring-inset ring-green-700/10 dark:ring-green-600/30">
										{{ $selectedEvent }}
									</span>
								@endif

								@if($selectedSeverity)
									<span class="inline-flex items-center rounded-md bg-amber-50 dark:bg-amber-900/30 px-1.5 py-0.5 text-xs font-medium text-amber-700 dark:text-amber-300 ring-1 ring-inset ring-amber-700/10 dark:ring-amber-600/30">
										{{ ucfirst($selectedSeverity) }}
									</span>
								@endif
							</div>
						@endif
					</div>
					<button
							onclick="window.toggleFilters()"
							type="button"
							class="px-2 py-1 text-xs font-medium text-center text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-dark-600 rounded-md hover:bg-gray-300 dark:hover:bg-dark-500 focus:outline-none"
					>
						<span id="filterButtonText">Show Filters</span>
					</button>
				</div>

				<!-- Activity Log Filters -->
				<div
						id="filterPanel"
						style="display: none;"
						class="p-3 bg-gray-50 dark:bg-dark-800 border-b border-gray-200 dark:border-gray-700"
				>
					<!-- Active Filters Indicator -->
					@if($selectedUser || $selectedEvent || $selectedSeverity || $dateFrom || $dateTo)
						<div class="mb-3 flex flex-wrap gap-2">
							<span class="text-xs font-medium text-gray-700 dark:text-gray-300">Active Filters:</span>

							@if($selectedUser)
								@php
									$userName = collect($userOptions)->firstWhere('id', $selectedUser)['name'] ?? 'Unknown';
								@endphp
								<span class="inline-flex items-center rounded-md bg-blue-50 dark:bg-blue-900/30 px-2 py-1 text-xs font-medium text-blue-700 dark:text-blue-300 ring-1 ring-inset ring-blue-700/10 dark:ring-blue-600/30">
									User: {{ $userName }}
								</span>
							@endif

							@if($selectedEvent)
								<span class="inline-flex items-center rounded-md bg-green-50 dark:bg-green-900/30 px-2 py-1 text-xs font-medium text-green-700 dark:text-green-300 ring-1 ring-inset ring-green-700/10 dark:ring-green-600/30">
									Event: {{ $selectedEvent }}
								</span>
							@endif

							@if($selectedSeverity)
								<span class="inline-flex items-center rounded-md bg-amber-50 dark:bg-amber-900/30 px-2 py-1 text-xs font-medium text-amber-700 dark:text-amber-300 ring-1 ring-inset ring-amber-700/10 dark:ring-amber-600/30">
									Severity: {{ ucfirst($selectedSeverity) }}
								</span>
							@endif

							@if($dateFrom)
								<span class="inline-flex items-center rounded-md bg-purple-50 dark:bg-purple-900/30 px-2 py-1 text-xs font-medium text-purple-700 dark:text-purple-300 ring-1 ring-inset ring-purple-700/10 dark:ring-purple-600/30">
									From: {{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }}
								</span>
							@endif

							@if($dateTo)
								<span class="inline-flex items-center rounded-md bg-purple-50 dark:bg-purple-900/30 px-2 py-1 text-xs font-medium text-purple-700 dark:text-purple-300 ring-1 ring-inset ring-purple-700/10 dark:ring-purple-600/30">
									To: {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}
								</span>
							@endif
						</div>
					@endif

					<div class="flex flex-col md:flex-row gap-2 mb-2">
						<!-- User Filter -->
						<div class="w-full md:w-1/4">
							<label
									for="userFilter"
									class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">User</label>
							<select
									id="userFilter"
									wire:model.live="selectedUser"
									wire:change="loadLogs"
									class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-dark-900 dark:text-white shadow-sm text-sm"
							>
								<option value="">All Users</option>
								@foreach($userOptions as $user)
									<option value="{{ $user['id'] }}">{{ $user['name'] }}</option>
								@endforeach
							</select>
						</div>

						<!-- Event Filter -->
						<div class="w-full md:w-1/4">
							<label
									for="eventFilter"
									class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Event</label>
							<select
									id="eventFilter"
									wire:model.live="selectedEvent"
									wire:change="loadLogs"
									class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-dark-900 dark:text-white shadow-sm text-sm"
							>
								<option value="">All Events</option>
								@foreach($eventOptions as $event)
									<option value="{{ $event }}">{{ $event }}</option>
								@endforeach
							</select>
						</div>

						<!-- Severity Filter -->
						<div class="w-full md:w-1/4">
							<label
									for="severityFilter"
									class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Severity</label>
							<select
									id="severityFilter"
									wire:model.live="selectedSeverity"
									wire:change="loadLogs"
									class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-dark-900 dark:text-white shadow-sm text-sm"
							>
								<option value="">All Severities</option>
								@foreach($severityOptions as $severity)
									<option value="{{ $severity }}">{{ ucfirst($severity) }}</option>
								@endforeach
							</select>
						</div>

						<!-- Reset Button -->
						<div class="w-full md:w-1/4 flex items-end">
							<button
									wire:click="resetFilters"
									type="button"
									class="w-full px-3 py-2 text-xs font-medium text-center text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-blue-700"
							>
								Reset Filters
							</button>
						</div>
					</div>

					<!-- Date Range Filter -->
					<div class="flex flex-col md:flex-row gap-2">
						<div class="w-full md:w-1/2">
							<label
									for="dateFrom"
									class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">From
							                                                                                Date</label>
							<input
									type="date"
									id="dateFrom"
									wire:model.live="dateFrom"
									wire:change="loadLogs"
									class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-dark-900 dark:text-white shadow-sm text-sm"
							>
						</div>
						<div class="w-full md:w-1/2">
							<label
									for="dateTo"
									class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">To
							                                                                                Date</label>
							<input
									type="date"
									id="dateTo"
									wire:model.live="dateTo"
									wire:change="loadLogs"
									class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-dark-900 dark:text-white shadow-sm text-sm"
							>
						</div>
					</div>
				</div>

				<div class="space-y-3 bg-gray-100/60 dark:bg-dark-700 p-4 rounded-lg max-h-[400px] overflow-y-auto">
					@if(count($recentLogs) > 0)
						@foreach($recentLogs as $log)
							<div class="flex items-start p-2 rounded-md hover:bg-gray-200 dark:hover:bg-dark-600 transition-colors group">
								<div class="text-emerald-500 mr-3 flex-shrink-0 mt-1">
									@if(str_contains($log->event, 'created'))
										<svg
												xmlns="http://www.w3.org/2000/svg"
												class="h-5 w-5"
												viewBox="0 0 20 20"
												fill="currentColor">
											<path
													fill-rule="evenodd"
													d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"
													clip-rule="evenodd" />
										</svg>
									@elseif(str_contains($log->event, 'updated') || str_contains($log->event, 'changed'))
										<svg
												xmlns="http://www.w3.org/2000/svg"
												class="h-5 w-5"
												viewBox="0 0 20 20"
												fill="currentColor">
											<path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
										</svg>
									@elseif(str_contains($log->event, 'deleted') || str_contains($log->event, 'removed'))
										<svg
												xmlns="http://www.w3.org/2000/svg"
												class="h-5 w-5 text-red-500"
												viewBox="0 0 20 20"
												fill="currentColor">
											<path
													fill-rule="evenodd"
													d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
													clip-rule="evenodd" />
										</svg>
									@else
										<svg
												xmlns="http://www.w3.org/2000/svg"
												class="h-5 w-5"
												viewBox="0 0 20 20"
												fill="currentColor">
											<path
													fill-rule="evenodd"
													d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
													clip-rule="evenodd" />
										</svg>
									@endif
								</div>
								<div class="flex-1">
									<h3 class="font-medium text-gray-900 dark:text-white text-sm group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">{{ $log->headline }}</h3>
									<div class="flex justify-between items-center mt-1">
										<p class="text-xs text-gray-500 dark:text-gray-400">
											<span class="inline-flex items-center rounded-md bg-gray-50 dark:bg-dark-600 px-1.5 py-0.5 text-xs font-medium text-gray-600 dark:text-gray-300 ring-1 ring-inset ring-gray-500/10 dark:ring-gray-400/20">
												{{ $log->event }}
											</span>
										</p>
										<span class="text-xs text-gray-400 dark:text-gray-500">
														{{ $log->occurred_at?->diffForHumans() ?? '' }}
										</span>
									</div>
									@if($log->description)
										<p class="text-xs text-gray-600 dark:text-gray-300 mt-1">
											{{ Str::limit($log->description, 50, '...') }}
										</p>
									@endif
								</div>
							</div>
							@if(!$loop->last)
								<div class="border-b border-gray-200 dark:border-gray-700"></div>
							@endif
						@endforeach
					@else
						<p class="text-sm text-center py-4">
							<span class="block mb-2">
								<svg
										xmlns="http://www.w3.org/2000/svg"
										class="h-10 w-10 mx-auto text-gray-400"
										fill="none"
										viewBox="0 0 24 24"
										stroke="currentColor">
									<path
											stroke-linecap="round"
											stroke-linejoin="round"
											stroke-width="2"
											d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
								</svg>
							</span>
							No recent activity found
						</p>
					@endif
				</div>
			</x-card>
		</div>
	</div>
</div>

@push('scripts')
	<script>
		// Filter toggle function
		window.toggleFilters = function () {
			const filterPanel = document.getElementById('filterPanel')
			const buttonText = document.getElementById('filterButtonText')

			if (filterPanel.style.display === 'none') {
				filterPanel.style.display = 'block'
				buttonText.textContent = 'Hide Filters'
			} else {
				filterPanel.style.display = 'none'
				buttonText.textContent = 'Show Filters'
			}
		}

		document.addEventListener('DOMContentLoaded', function () {
			// This ensures ApexCharts is available
			if (typeof ApexCharts === 'undefined') {
				console.error('ApexCharts is not loaded. Loading from CDN...')
				const script = document.createElement('script')
				script.src = 'https://cdn.jsdelivr.net/npm/apexcharts'
				script.onload = function () {
					console.log('ApexCharts loaded from CDN')
					// Dispatch an event to initialize charts
					window.dispatchEvent(new CustomEvent('apexcharts-loaded'))
				}
				document.head.appendChild(script)
			} else {
				// If ApexCharts is already available, dispatch the event
				window.dispatchEvent(new CustomEvent('apexcharts-loaded'))
			}
		})
	</script>
	<style>
        /* Custom styling for ApexCharts in dark mode */
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

        /* Ensure all ApexCharts text is visible in dark mode */
        .dark .apexcharts-text,
        .dark .apexcharts-title-text,
        .dark .apexcharts-legend-text {
            fill: #FFFFFF !important;
            color: #FFFFFF !important;
        }

        /* Enhanced tooltip styling for better readability */
        .apexcharts-tooltip {
            background-color: rgba(255, 255, 255, 0.95) !important;
            color: #1F2937 !important;
            border: 1px solid #E5E7EB !important;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
        }

        .apexcharts-tooltip-title {
            background-color: #F3F4F6 !important;
            border-bottom: 1px solid #E5E7EB !important;
            font-weight: 600 !important;
            color: #111827 !important;
        }

        .dark .apexcharts-tooltip {
            background-color: rgba(31, 41, 55, 0.95) !important;
            color: #F9FAFB !important;
            border: 1px solid #374151 !important;
        }

        .dark .apexcharts-tooltip-title {
            background-color: #111827 !important;
            border-bottom: 1px solid #374151 !important;
            color: #F3F4F6 !important;
        }

        /* Empty state styling */
        .chart-empty-state {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 240px;
            color: #6B7280;
            font-size: 0.875rem;
            text-align: center;
        }
	</style>

	<!-- 
	Dark Mode Fix Notes:
	1. Changed label colors to pure white (#FFFFFF) for maximum visibility in dark mode
	2. Added CSS rules to ensure all ApexCharts text elements are visible in dark mode
	3. Implemented complete chart reinitialization on theme changes for more reliable updates
	4. Added a manual theme toggle button as a fallback if automatic detection fails
	5. Added debug logging to help diagnose theme detection issues
	-->
@endpush

