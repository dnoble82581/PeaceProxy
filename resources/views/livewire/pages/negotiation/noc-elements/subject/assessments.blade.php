<?php

	use App\Models\Assessment;
	use App\Models\AssessmentTemplate;
	use App\Models\AssessmentTemplateQuestion;
	use App\Models\AssessmentQuestionsAnswer;
	use App\Models\Negotiation;
	use App\Models\Subject;
	use Livewire\Volt\Component;
	use Livewire\WithFileUploads;
	use Livewire\Attributes\Rule;
	use Illuminate\View\View;

	new class extends Component {
		use WithFileUploads;

		public Subject $subject;
		public Negotiation $negotiation;

		// Properties for assessment management
		public $templates = [];
		public $assessments = [];

		#[Rule('required|exists:assessment_templates,id')]
		public $selectedTemplateId = null;

		public $questions = [];
		public $answers = [];
		public $assessment = null;

		// Properties for UI state
		public $showTemplateSelection = false;
		public $showQuestions = false;
		public $errorMessage = '';
		public $showQuestionsSlide = false;
		public $showCreateForm = false;

		public function mount($subjectId, $negotiationId)
		{
			$this->subject = Subject::findOrFail($subjectId);
			$this->negotiation = Negotiation::findOrFail($negotiationId);
			$this->loadTemplates();
			$this->loadAssessments();
		}

		public function loadAssessments()
		{
			$this->assessments = $this->subject->riskAssessments()
				->with('assessmentTemplate')
				->orderBy('created_at', 'desc')
				->get();
		}

 	public function loadTemplates()
 	{
 		$this->templates = AssessmentTemplate::where('tenant_id', tenant()->id)
 			->orderBy('created_at', 'desc')
 			->get();
 	}

		// Define validation rules
		public function rules()
		{
			return [
				'selectedTemplateId' => 'required|exists:assessment_templates,id',
			];
		}

		public function selectTemplate()
		{
			$this->validate();

			$template = AssessmentTemplate::with('questions')->find($this->selectedTemplateId);
			$this->questions = $template->questions;

			// Initialize answers array with empty values
			$this->answers = [];
			foreach ($this->questions as $question) {
				$questionType = $question->question_type;

				// Initialize different answer types based on question type
				if (in_array($questionType, ['checkbox', 'multiselect'])) {
					$this->answers[$question->id] = [];
				} elseif ($questionType === 'boolean') {
					$this->answers[$question->id] = 'false';
				} else {
					$this->answers[$question->id] = '';
				}
			}

			// Create a new assessment
			$this->assessment = Assessment::create([
				'tenant_id' => tenant()->id,
				'assessment_template_id' => $this->selectedTemplateId,
				'negotiation_id' => $this->negotiation->id,
				'subject_id' => $this->subject->id,
				'started_at' => now(),
				'title' => $template->name.' - '.$this->subject->name,
			]);

			// Show the slide with questions instead of changing the view
			$this->showQuestionsSlide = true;
		}

		public function closeQuestionsSlide()
		{
			$this->showQuestionsSlide = false;

			// If the assessment was not completed, delete it
			if ($this->assessment && !$this->assessment->completed_at) {
				$this->assessment->delete();
				$this->assessment = null;
			}

			// Reset answers
			$this->answers = [];
		}

		public function submitAssessment()
		{
			// Validate required questions
			$requiredQuestions = $this->questions->where('is_required', true);
			$dynamicRules = [];

			foreach ($requiredQuestions as $question) {
				$rule = 'required';

				if (in_array($question->question_type, ['checkbox', 'multiselect'])) {
					$rule .= '|array|min:1';
				}

				$dynamicRules['answers.'.$question->id] = $rule;
			}

			// Merge dynamic rules with static rules from rules() method
			$allRules = array_merge($this->rules(), $dynamicRules);

			$this->validate($allRules);

			// Initialize score counter for 'yes' answers
			$yesCount = 0;

			// Save answers to database
			foreach ($this->questions as $question) {
				$answer = $this->answers[$question->id] ?? null;
				$questionType = $question->question_type;

				// Skip if no answer provided for non-required questions
				if (empty($answer) && !$question->is_required) {
					continue;
				}

				// Special handling for file uploads
				if ($questionType === 'file' && !empty($answer)) {
					// Store the file and get the path
					$path = $answer->store('assessment-files', 'public');
					$answer = $path;
				}

				// Special handling for rating type
				if ($questionType === 'rating' && !is_null($answer)) {
					// Ensure rating is an integer
					$answer = (int) $answer;
				}

				// Count 'yes' answers for boolean questions
				if ($questionType === 'boolean' && $answer === 'true') {
					$yesCount++;
				}

				AssessmentQuestionsAnswer::create([
					'assessment_id' => $this->assessment->id,
					'assessment_template_question_id' => $question->id,
					'answer' => json_encode($answer),
				]);
			}

			// Mark assessment as completed and update the score
			$this->assessment->update([
				'completed_at' => now(),
				'score' => $yesCount,
			]);

			// Close the slides
			$this->showQuestionsSlide = false;
			$this->showCreateForm = false;

			// Show success notification
			$this->dispatch('notify', [
				'message' => 'Assessment completed successfully!',
				'type' => 'success'
			]);

			// Reload assessments to show the new one
			$this->loadAssessments();
		}

		public function startNewAssessment()
		{
			$this->reset([
				'selectedTemplateId',
				'questions',
				'answers',
				'assessment'
			]);

			$this->showCreateForm = true;
		}

		public function showCreateForm()
		{
			dd('here');
			$this->showCreateForm = true;
		}

		public function hideCreateForm()
		{
			$this->showCreateForm = false;
		}

		public function deleteAssessment($assessmentId)
		{
			$assessment = Assessment::find($assessmentId);
			if ($assessment) {
				// Delete related answers first
				$assessment->answers()->delete();
				// Then delete the assessment
				$assessment->delete();

				// Reload assessments
				$this->loadAssessments();
			}
		}
	}

?>

<div>
	<!-- Removed debug section -->

	<div
			x-data="{ showNotification: false, message: '', type: '' }"
			@notify.window="showNotification = true; message = $event.detail.message; type = $event.detail.type; setTimeout(() => showNotification = false, 3000)">
		<!-- Notification -->
		<div
				x-show="showNotification"
				x-transition:enter="transition ease-out duration-300"
				x-transition:enter-start="opacity-0 transform scale-90"
				x-transition:enter-end="opacity-100 transform scale-100"
				x-transition:leave="transition ease-in duration-300"
				x-transition:leave-start="opacity-100 transform scale-100"
				x-transition:leave-end="opacity-0 transform scale-90"
				x-bind:class="{ 'bg-green-100 border-green-400 text-green-700': type === 'success', 'bg-red-100 border-red-400 text-red-700': type === 'error' }"
				class="border px-4 py-3 rounded relative mb-2"
				role="alert">
			<span
					class="block sm:inline"
					x-text="message"></span>
		</div>

		<!-- Assessments Table View -->
		<div class="mt-2 flow-root overflow-hidden rounded-t-lg">
			<div class="">
				<table class="w-full text-left">
					<thead class="dark:bg-dark-600">
					<tr>
						<th
								scope="col"
								class="relative isolate px-3 text-left text-xs font-semibold text-primary-950 dark:text-dark-100">
							Title
							<div class="absolute inset-y-0 right-full -z-10 w-screen border-b border-b-gray-200"></div>
							<div class="absolute inset-y-0 left-0 -z-10 w-screen border-b border-b-gray-200"></div>
						</th>
						<th
								scope="col"
								class="hidden px-3 py-2 text-left text-xs font-semibold text-primary-950 dark:text-dark-100 sm:table-cell">
							Score
						</th>
						<th
								scope="col"
								class="hidden px-3 py-2 text-left text-xs font-semibold text-primary-950 dark:text-dark-100 md:table-cell">
							Status
						</th>
						<th
								scope="col"
								class="px-3 py-2 text-left text-xs font-semibold text-primary-950 dark:text-dark-100">
							Date
						</th>
						<th
								scope="col"
								class="py-2 pl-3">
							<span class="sr-only">Actions</span>
						</th>
						<th
								scope="col"
								class="relative">
							<div>
								<x-button.circle
										color=""
										wire:click="$toggle('showCreateForm')"
										sm
										flat
										icon="plus" />
							</div>
						</th>
					</tr>
					</thead>
					<tbody>
					@forelse($assessments as $assessment)
						<tr>
							<td class="relative pl-3 text-xs font-medium text-primary-950 dark:text-dark-100">
								{{ $assessment->title }}
								<div class="absolute right-full bottom-0 h-px w-screen bg-gray-100"></div>
								<div class="absolute bottom-0 left-0 h-px w-screen bg-gray-100"></div>
							</td>
							<td class="hidden px-3 py-2 text-xs dark:text-dark-400 text-gray-500 sm:table-cell">
								{{ $assessment->score }}
							</td>
							<td class="hidden px-3 py-2 text-xs dark:text-dark-400 text-gray-500 md:table-cell">
								@if($assessment->completed_at)
									<span class="text-green-500">Completed</span>
								@else
									<span class="text-yellow-500">In Progress</span>
								@endif
							</td>
							<td class="px-3 py-2 text-xs dark:text-dark-400 text-gray-500">
								@if($assessment->completed_at)
									@if(is_int($assessment->completed_at))
										{{ \Carbon\Carbon::createFromTimestamp($assessment->completed_at)->format('M d, Y') }}
									@else
										{{ $assessment->completed_at->format('M d, Y') }}
									@endif
								@else
									@if(is_int($assessment->started_at))
										{{ \Carbon\Carbon::createFromTimestamp($assessment->started_at)->format('M d, Y') }}
									@else
										{{ $assessment->started_at->format('M d, Y') }}
									@endif
								@endif
							</td>
							<td class="text-right">
								<x-button.circle
										wire:click="deleteAssessment({{ $assessment->id }})"
										flat
										color="red"
										icon="trash"
										sm />
							</td>
						</tr>
					@empty
						<tr>
							<td
									colspan="5"
									class="text-center p-4 text-gray-500">
								No assessments found for this subject.
								<p class="mt-2">
									Click the <span class="inline-flex items-center"><svg
												class="w-4 h-4 text-gray-500"
												fill="none"
												stroke="currentColor"
												viewBox="0 0 24 24"
												xmlns="http://www.w3.org/2000/svg"><path
													stroke-linecap="round"
													stroke-linejoin="round"
													stroke-width="2"
													d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg></span> button in
									the top-right corner to create a new assessment.
								</p>
							</td>
						</tr>
					@endforelse
					</tbody>
				</table>
			</div>
		</div>

		<!-- Template Selection Slide -->
		<x-slide
				wire="showCreateForm"
				size="md">
			<x-slot:title>
				<div class="flex justify-between items-center">
					<h1 class="text-xl font-bold">Select Assessment Template</h1>
				</div>
			</x-slot:title>

			<div class="p-4">
				<p class="text-gray-600 dark:text-gray-400 mb-6">
					Select a template to start a new assessment for {{ $subject->name }}.
				</p>

				@if(count($templates) > 0)
					<div class="space-y-4">
						<div>
							<x-select.styled
									wire:model="selectedTemplateId"
									placeholder="Select a template"
									:options="$templates->map(fn($template) => ['value' => $template->id, 'label' => $template->name])->toArray()" />
							@error('selectedTemplateId')
							<span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
						</div>

						<div class="flex justify-end space-x-2 mt-6">
							<x-button
									color="gray"
									wire:click="hideCreateForm"
									class="mr-2">
								Cancel
							</x-button>
							<x-button
									text="Start Assessment"
									color="blue"
									wire:click="selectTemplate"
							/>
						</div>
					</div>
				@else
					<div class="bg-white dark:bg-dark-800 rounded-lg shadow p-3 text-center">
						<p class="text-gray-500 dark:text-gray-400 text-sm">No assessment templates available. Please
						                                                    create a template first.</p>
					</div>
				@endif
			</div>
		</x-slide>

		<!-- Questions Slide -->
		<x-slide
				wire="showQuestionsSlide"
				size="xl">
			<x-slot:title>
				<div class="flex justify-between items-center">
					<h1 class="text-xl font-bold">{{ $subject->name }} Assessment</h1>
				</div>
			</x-slot:title>

			<div class="p-4">
				<p class="text-gray-600 dark:text-gray-400 mb-6">
					Complete the assessment by answering the questions below.
				</p>

				<form wire:submit.prevent="submitAssessment">
					<div class="space-y-6">
						@if(count($questions) > 0)
							@foreach($questions as $question)
								<div class="border-b border-gray-200 dark:border-dark-600 pb-4 mb-4 last:border-b-0">
									<div class="mb-2">
										<label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
											{{ $question->question }}
											@if($question->is_required)
												<span class="text-red-500">*</span>
											@endif
										</label>
									</div>

									@switch($question->question_type)
										@case('text')
											<x-input
													wire:model="answers.{{ $question->id }}"
													placeholder="Enter your answer" />
											@break

										@case('textarea')
											<x-textarea
													wire:model="answers.{{ $question->id }}"
													placeholder="Enter your answer"
													rows="3" />
											@break

										@case('number')
											<x-input
													type="number"
													wire:model="answers.{{ $question->id }}"
													placeholder="Enter a number" />
											@break

										@case('boolean')
											<div class="space-y-2">
												<div class="flex items-center">
													<x-radio
															wire:model="answers.{{ $question->id }}"
															value="true"
															label="Yes" />
												</div>
												<div class="flex items-center">
													<x-radio
															wire:model="answers.{{ $question->id }}"
															value="false"
															label="No" />
												</div>
											</div>
											@break

										@case('select')
											<x-select.styled
													wire:model="answers.{{ $question->id }}"
													placeholder="Select an option"
													:options="collect(json_decode($question->options))->map(fn($option) => ['value' => $option, 'label' => $option])->toArray()" />
											@break

										@case('multiselect')
											<div class="space-y-2">
												@foreach(json_decode($question->options) as $option)
													<div class="flex items-center">
														<x-checkbox
																wire:model="answers.{{ $question->id }}"
																value="{{ $option }}"
																label="{{ $option }}" />
													</div>
												@endforeach
											</div>
											@break

										@case('radio')
											<div class="space-y-2">
												@foreach(json_decode($question->options) as $option)
													<div class="flex items-center">
														<x-radio
																wire:model="answers.{{ $question->id }}"
																value="{{ $option }}"
																label="{{ $option }}" />
													</div>
												@endforeach
											</div>
											@break

										@case('checkbox')
											<div class="space-y-2">
												@foreach(json_decode($question->options) as $option)
													<div class="flex items-center">
														<x-checkbox
																wire:model="answers.{{ $question->id }}"
																value="{{ $option }}"
																label="{{ $option }}" />
													</div>
												@endforeach
											</div>
											@break

										@case('date')
											<x-input
													type="date"
													wire:model="answers.{{ $question->id }}" />
											@break

										@case('time')
											<x-input
													type="time"
													wire:model="answers.{{ $question->id }}" />
											@break

										@case('datetime')
											<x-input
													type="datetime-local"
													wire:model="answers.{{ $question->id }}" />
											@break

										@case('rating')
											<div class="flex space-x-2">
												@for($i = 1; $i <= 5; $i++)
													<button
															type="button"
															wire:click="$set('answers.{{ $question->id }}', {{ $i }})"
															class="p-2 rounded-full {{ $answers[$question->id] == $i ? 'bg-blue-500 text-white' : 'bg-gray-200 dark:bg-dark-600' }}">
														{{ $i }}
													</button>
												@endfor
											</div>
											@break

										@case('file')
											<div>
												<input
														type="file"
														class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-dark-700 dark:border-dark-600 dark:placeholder-gray-400"
														wire:model.live="answers.{{ $question->id }}" />
												<p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
													Select a file to upload
												</p>
												@if(isset($answers[$question->id]) && $answers[$question->id])
													<div class="mt-2 text-sm text-gray-600 dark:text-gray-300">
														File
														selected: {{ is_string($answers[$question->id]) ? basename($answers[$question->id]) : $answers[$question->id]->getClientOriginalName() }}
													</div>
												@endif
											</div>
											@break

										@default
											<x-input
													wire:model="answers.{{ $question->id }}"
													placeholder="Enter your answer" />
									@endswitch

									@error('answers.' . $question->id)
									<span class="text-red-500 text-sm">{{ $message }}</span> @enderror
								</div>
							@endforeach

							<div class="flex justify-end space-x-2">
								<x-button
										color="gray"
										wire:click="closeQuestionsSlide"
										class="mr-2">
									Cancel
								</x-button>
								<x-button
										color="blue"
										type="submit"
										wire:loading.attr="disabled">
									<span wire:loading.remove>Submit Assessment</span>
									<span wire:loading>Saving...</span>
								</x-button>
							</div>
						@else
							<div class="text-center text-gray-500 dark:text-gray-400">
								<p>No questions found for this template.</p>
							</div>
						@endif
					</div>
				</form>
			</div>
		</x-slide>

		<!-- Success message is now handled through the notification system -->
	</div>
</div>
