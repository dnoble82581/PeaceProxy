<?php

	use App\Models\AssessmentTemplate;
	use App\Models\AssessmentTemplateQuestion;
	use Livewire\Attributes\Computed;
	use Livewire\Attributes\Layout;
	use Livewire\Attributes\Rule;
	use Livewire\Volt\Component;
	use Illuminate\Support\Facades\Http;

 new #[Layout('components.layouts.app'), \Livewire\Attributes\Title('Assessments - Peace Proxy')] class extends Component {
		// Properties for template management
		public $templates = [];
		public $showCreateTemplateModal = false;
		public $showEditTemplateModal = false;
		public $showDeleteTemplateModal = false;
		public $currentTemplate = null;

		#[Rule('required|string|max:255')]
		public $templateName = '';

		#[Rule('nullable|string')]
		public $templateDescription = '';

		// Properties for template question management
		public $showTemplateQuestions = false;
		public $selectedTemplate = null;
		public $showCreateQuestionModal = false;
		public $showEditQuestionModal = false;
		public $showDeleteQuestionModal = false;
		public $currentQuestion = null;

		#[Rule('required|string')]
		public $questionText = '';

		#[Rule('required|string')]
		public $questionType = '';

		#[Rule('required|string')]
		public $questionCategory = '';

		#[Rule('required|boolean')]
		public $isRequired = false;

		public $questionOptions = [];
		public $newOption = '';

		// Method to open create question modal and reset form
		public function openCreateQuestionModal()
		{
			$this->reset([
				'questionText', 'questionType', 'questionCategory', 'isRequired', 'questionOptions', 'newOption'
			]);
			$this->showCreateQuestionModal = true;
		}

		// Fetch data on mount
		public function mount()
		{
			$this->loadTemplates();
		}

		// Load all templates for the current tenant
		public function loadTemplates()
		{
			$this->templates = AssessmentTemplate::where('tenant_id', tenant()->id)
				->orderBy('name')
				->get();
		}

		// Create a new template
		public function createTemplate()
		{
			$this->validate([
				'templateName' => 'required|string|max:255',
				'templateDescription' => 'nullable|string',
			]);

			AssessmentTemplate::create([
				'name' => $this->templateName,
				'description' => $this->templateDescription,
				'tenant_id' => tenant()->id,
			]);

			$this->reset(['templateName', 'templateDescription', 'showCreateTemplateModal']);
			$this->loadTemplates();

			$this->dispatch('notify', message: 'Template created successfully!', type: 'success');
		}

		// Edit template - prepare modal
		public function editTemplate($templateId)
		{
			$this->currentTemplate = AssessmentTemplate::find($templateId);

			if (!$this->currentTemplate) {
				$this->dispatch('notify', message: 'Template not found!', type: 'error');
				return;
			}

			$this->templateName = $this->currentTemplate->name;
			$this->templateDescription = $this->currentTemplate->description;
			$this->showEditTemplateModal = true;
		}

		// Update template
		public function updateTemplate()
		{
			$this->validate([
				'templateName' => 'required|string|max:255',
				'templateDescription' => 'nullable|string',
			]);

			if (!$this->currentTemplate) {
				$this->dispatch('notify', message: 'Template not found!', type: 'error');
				return;
			}

			$this->currentTemplate->update([
				'name' => $this->templateName,
				'description' => $this->templateDescription,
			]);

			$this->reset(['templateName', 'templateDescription', 'showEditTemplateModal', 'currentTemplate']);
			$this->loadTemplates();

			$this->dispatch('notify', message: 'Template updated successfully!', type: 'success');
		}

		// Delete template - prepare modal
		public function confirmDeleteTemplate($templateId)
		{
			$this->currentTemplate = AssessmentTemplate::find($templateId);

			if (!$this->currentTemplate) {
				$this->dispatch('notify', message: 'Template not found!', type: 'error');
				return;
			}

			$this->showDeleteTemplateModal = true;
		}

		// Delete template
		public function deleteTemplate()
		{
			if (!$this->currentTemplate) {
				$this->dispatch('notify', message: 'Template not found!', type: 'error');
				return;
			}

			// Check if template has questions
			$questionsCount = $this->currentTemplate->questions()->count();

			if ($questionsCount > 0) {
				// Delete all questions first
				$this->currentTemplate->questions()->delete();
			}

			// Delete the template
			$this->currentTemplate->delete();

			$this->reset(['showDeleteTemplateModal', 'currentTemplate']);
			$this->loadTemplates();

			$this->dispatch('notify', message: 'Template Deleted Successfully!', type: 'success');
		}

		// View template questions
		public function viewTemplateQuestions($templateId)
		{
			$this->selectedTemplate = AssessmentTemplate::with('questions')->find($templateId);

			if (!$this->selectedTemplate) {
				$this->dispatch('notify', message: 'Template not found!', type: 'error');
				return;
			}

			$this->showTemplateQuestions = true;
		}

		// Back to templates list
		public function backToTemplates()
		{
			$this->reset(['showTemplateQuestions', 'selectedTemplate']);
		}

		// Add option to question
		public function addOption()
		{
			if (!empty($this->newOption)) {
				$this->questionOptions[] = $this->newOption;
				$this->newOption = '';
			}
		}

		// Remove option from question
		public function removeOption($index)
		{
			if (isset($this->questionOptions[$index])) {
				unset($this->questionOptions[$index]);
				$this->questionOptions = array_values($this->questionOptions);
			}
		}

		// Create a new question
		public function createQuestion()
		{
			$this->validate([
				'questionText' => 'required|string',
				'questionType' => 'required|string',
				'questionCategory' => 'required|string',
				'isRequired' => 'required|boolean',
			]);

			// For select, multiselect, radio, checkbox types, options are required
			if (in_array($this->questionType,
					['select', 'multiselect', 'radio', 'checkbox']) && empty($this->questionOptions)) {
				$this->addError('questionOptions', 'Options are required for this question type.');
				return;
			}

			AssessmentTemplateQuestion::create([
				'assessment_template_id' => $this->selectedTemplate->id,
				'question' => $this->questionText,
				'question_type' => $this->questionType,
				'question_category' => $this->questionCategory,
				'options' => json_encode($this->questionOptions),
				'is_required' => $this->isRequired,
			]);

			$this->reset([
				'questionText', 'questionType', 'questionCategory', 'isRequired', 'questionOptions', 'newOption',
				'showCreateQuestionModal'
			]);
			$this->selectedTemplate = AssessmentTemplate::with('questions')->find($this->selectedTemplate->id);

			$this->dispatch('notify', message: 'Question added successfully!', type: 'success');
		}

		// Edit question - prepare modal
		public function editQuestion($questionId)
		{
			$this->currentQuestion = AssessmentTemplateQuestion::find($questionId);

			if (!$this->currentQuestion) {
				$this->dispatch('notify', message: 'Question not found!', type: 'error');
				return;
			}

			// Reset the newOption field before populating the form
			$this->reset(['newOption']);

			$this->questionText = $this->currentQuestion->question;
			$this->questionType = $this->currentQuestion->question_type;
			$this->questionCategory = $this->currentQuestion->question_category;
			$this->isRequired = $this->currentQuestion->is_required;
			$this->questionOptions = json_decode($this->currentQuestion->options, true) ?? [];

			$this->showEditQuestionModal = true;
		}

		// Update question
		public function updateQuestion()
		{
			$this->validate([
				'questionText' => 'required|string',
				'questionType' => 'required|string',
				'questionCategory' => 'required|string',
				'isRequired' => 'required|boolean',
			]);

			// For select, multiselect, radio, checkbox types, options are required
			if (in_array($this->questionType,
					['select', 'multiselect', 'radio', 'checkbox']) && empty($this->questionOptions)) {
				$this->addError('questionOptions', 'Options are required for this question type.');
				return;
			}

			if (!$this->currentQuestion) {
				$this->dispatch('notify', message: 'Question not found!', type: 'error');
				return;
			}

			$this->currentQuestion->update([
				'question' => $this->questionText,
				'question_type' => $this->questionType,
				'question_category' => $this->questionCategory,
				'options' => json_encode($this->questionOptions),
				'is_required' => $this->isRequired,
			]);

			$this->reset([
				'questionText', 'questionType', 'questionCategory', 'isRequired', 'questionOptions', 'newOption',
				'showEditQuestionModal', 'currentQuestion'
			]);
			$this->selectedTemplate = AssessmentTemplate::with('questions')->find($this->selectedTemplate->id);

			$this->dispatch('notify', message: 'Question updated successfully!', type: 'success');
		}

		// Delete question - prepare modal
		public function confirmDeleteQuestion($questionId)
		{
			$this->currentQuestion = AssessmentTemplateQuestion::find($questionId);

			if (!$this->currentQuestion) {
				$this->dispatch('notify', message: 'Question not found!', type: 'error');
				return;
			}

			$this->showDeleteQuestionModal = true;
		}

		// Delete question
		public function deleteQuestion()
		{
			if (!$this->currentQuestion) {
				$this->dispatch('notify', message: 'Question not found!', type: 'error');
				return;
			}

			$this->currentQuestion->delete();

			$this->reset(['showDeleteQuestionModal', 'currentQuestion']);
			$this->selectedTemplate = AssessmentTemplate::with('questions')->find($this->selectedTemplate->id);

			$this->dispatch('notify', message: 'Question deleted successfully!', type: 'success'); // named args
		}

		// Get question types from API
		#[Computed]
		public function questionTypes()
		{
			try {
				$response = Http::get(route('enums.response-types'));
				return $response->json() ?? [];
			} catch (\Exception $e) {
				return [];
			}
		}

		// Get question categories from API
		#[Computed]
		public function questionCategories()
		{
			try {
				$response = Http::get(route('enums.question-categories'));
				return $response->json() ?? [];
			} catch (\Exception $e) {
				return [];
			}
		}
	}

?>

<div>
	<!-- Templates List View -->
	<div
			class="p-4"
			x-data="{ showNotification: false, message: '', type: '' }"
			@notify.window="
            showNotification = true;
            message = $event.detail.message ?? ($event.detail[0] ?? '');
            type = $event.detail.type ?? 'success';
            setTimeout(() => showNotification = false, 3000);
    "
	>
		<div
				x-show="showNotification"
				x-transition:enter="transition ease-out duration-300"
				x-transition:enter-start="opacity-0 transform scale-90"
				x-transition:enter-end="opacity-100 transform scale-100"
				x-transition:leave="transition ease-in duration-300"
				x-transition:leave-start="opacity-100 transform scale-100"
				x-transition:leave-end="opacity-0 transform scale-90"
				:class="{
            'bg-green-100 border-green-400 text-green-700': type === 'success',
            'bg-red-100 border-red-400 text-red-700': type === 'error'
        }"
				class="border px-4 py-3 rounded relative mb-4"
				role="alert"
		>
			<span
					class="block sm:inline"
					x-text="message"></span>
		</div>

		<!-- Templates View -->
		<div x-show="!$wire.showTemplateQuestions">
			<div class="flex justify-between items-center mb-6">
				<h1 class="text-2xl font-bold">Assessment Templates</h1>
				<x-button
						color="blue"
						wire:click="$set('showCreateTemplateModal', true)">
					Create Template
				</x-button>
			</div>

			<!-- Templates List -->
			<div class="bg-white dark:bg-dark-800 rounded-lg shadow overflow-hidden">
				@if(count($templates) > 0)
					<table class="min-w-full divide-y divide-gray-200 dark:divide-dark-600">
						<thead class="bg-gray-50 dark:bg-dark-700">
						<tr>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
								Name
							</th>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
								Description
							</th>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
								Questions
							</th>
							<th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
								Actions
							</th>
						</tr>
						</thead>
						<tbody class="bg-white dark:bg-dark-800 divide-y divide-gray-200 dark:divide-dark-600">
						@foreach($templates as $template)
							<tr>
								<td class="px-6 py-4 whitespace-nowrap">
									<div class="text-sm font-medium text-gray-900 dark:text-white">{{ $template->name }}</div>
								</td>
								<td class="px-6 py-4">
									<div class="text-sm text-gray-500 dark:text-gray-300">{{ $template->description ?? 'No description' }}</div>
								</td>
								<td class="px-6 py-4 whitespace-nowrap">
									<div class="text-sm text-gray-500 dark:text-gray-300">{{ $template->questions->count() }}
										questions
									</div>
								</td>
								<td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
									<div class="flex justify-end space-x-2">
										<x-button
												color="blue"
												size="sm"
												wire:click="viewTemplateQuestions({{ $template->id }})">
											Questions
										</x-button>
										<x-button
												color="yellow"
												size="sm"
												wire:click="editTemplate({{ $template->id }})">
											Edit
										</x-button>
										<x-button
												color="red"
												size="sm"
												wire:click="confirmDeleteTemplate({{ $template->id }})">
											Delete
										</x-button>
									</div>
								</td>
							</tr>
						@endforeach
						</tbody>
					</table>
				@else
					<div class="p-6 text-center text-gray-500 dark:text-gray-400">
						<p>No templates found. Create your first template to get started.</p>
					</div>
				@endif
			</div>

			<!-- Create Template Modal -->
			<x-modal wire="showCreateTemplateModal">
				<x-slot:title>Create Assessment Template</x-slot:title>

				<div class="space-y-4">
					<div>
						<x-input
								label="Template Name"
								wire:model="templateName"
								placeholder="Enter template name" />
						@error('templateName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
					</div>

					<div>
						<x-textarea
								label="Description (Optional)"
								wire:model="templateDescription"
								placeholder="Enter template description" />
						@error('templateDescription') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
					</div>
				</div>

				<x-slot:footer>
					<div class="flex justify-end space-x-2">
						<x-button
								color="gray"
								wire:click="$set('showCreateTemplateModal', false)">Cancel
						</x-button>
						<x-button
								color="blue"
								wire:click="createTemplate">Create Template
						</x-button>
					</div>
				</x-slot:footer>
			</x-modal>

			<!-- Edit Template Modal -->
			<x-modal wire="showEditTemplateModal">
				<x-slot:title>Edit Assessment Template</x-slot:title>

				<div class="space-y-4">
					<div>
						<x-input
								label="Template Name"
								wire:model="templateName"
								placeholder="Enter template name" />
						@error('templateName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
					</div>

					<div>
						<x-textarea
								label="Description (Optional)"
								wire:model="templateDescription"
								placeholder="Enter template description" />
						@error('templateDescription') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
					</div>
				</div>

				<x-slot:footer>
					<div class="flex justify-end space-x-2">
						<x-button
								color="gray"
								wire:click="$set('showEditTemplateModal', false)">Cancel
						</x-button>
						<x-button
								color="blue"
								wire:click="updateTemplate">Update Template
						</x-button>
					</div>
				</x-slot:footer>
			</x-modal>

			<!-- Delete Template Confirmation Modal -->
			<x-modal wire="showDeleteTemplateModal">
				<x-slot:title>Delete Assessment Template</x-slot:title>

				<div class="space-y-4">
					<p class="text-gray-700 dark:text-gray-300">
						Are you sure you want to delete this template? This action cannot be undone.
					</p>

					@if($currentTemplate && $currentTemplate->questions->count() > 0)
						<div
								class="bg-yellow-100 dark:bg-yellow-900 border-l-4 border-yellow-500 text-yellow-700 dark:text-yellow-300 p-4"
								role="alert">
							<p>This template has {{ $currentTemplate->questions->count() }} questions that will also be
							   deleted.</p>
						</div>
					@endif
				</div>

				<x-slot:footer>
					<div class="flex justify-end space-x-2">
						<x-button
								color="gray"
								wire:click="$set('showDeleteTemplateModal', false)">Cancel
						</x-button>
						<x-button
								color="red"
								wire:click="deleteTemplate">Delete Template
						</x-button>
					</div>
				</x-slot:footer>
			</x-modal>
		</div>

		<!-- Template Questions View -->
		<div x-show="$wire.showTemplateQuestions">
			<div class="flex justify-between items-center mb-6">
				<div>
					<x-button
							color="gray"
							wire:click="backToTemplates">
						<svg
								xmlns="http://www.w3.org/2000/svg"
								class="h-5 w-5 mr-1"
								viewBox="0 0 20 20"
								fill="currentColor">
							<path
									fill-rule="evenodd"
									d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z"
									clip-rule="evenodd" />
						</svg>
						Back to Templates
					</x-button>
				</div>
				<h1 class="text-2xl font-bold">{{ $selectedTemplate ? $selectedTemplate->name : 'Template' }}
					Questions</h1>
				<x-button
						color="blue"
						wire:click="openCreateQuestionModal">
					Add Question
				</x-button>
			</div>

			<!-- Questions List -->
			<div class="bg-white dark:bg-dark-800 rounded-lg shadow overflow-hidden">
				@if($selectedTemplate && count($selectedTemplate->questions) > 0)
					<table class="min-w-full divide-y divide-gray-200 dark:divide-dark-600">
						<thead class="bg-gray-50 dark:bg-dark-700">
						<tr>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
								Question
							</th>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
								Type
							</th>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
								Category
							</th>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
								Required
							</th>
							<th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
								Actions
							</th>
						</tr>
						</thead>
						<tbody class="bg-white dark:bg-dark-800 divide-y divide-gray-200 dark:divide-dark-600">
						@foreach($selectedTemplate->questions as $question)
							<tr>
								<td class="px-6 py-4">
									<div class="text-sm font-medium text-gray-900 dark:text-white">{{ $question->question }}</div>
								</td>
								<td class="px-6 py-4 whitespace-nowrap">
									<div class="text-sm text-gray-500 dark:text-gray-300">{{ $question->question_type }}</div>
								</td>
								<td class="px-6 py-4 whitespace-nowrap">
									<div class="text-sm text-gray-500 dark:text-gray-300">{{ $question->question_category }}</div>
								</td>
								<td class="px-6 py-4 whitespace-nowrap">
									<div class="text-sm text-gray-500 dark:text-gray-300">{{ $question->is_required ? 'Yes' : 'No' }}</div>
								</td>
								<td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
									<div class="flex justify-end space-x-2">
										<x-button
												color="yellow"
												size="sm"
												wire:click="editQuestion({{ $question->id }})">
											Edit
										</x-button>
										<x-button
												color="red"
												size="sm"
												wire:click="confirmDeleteQuestion({{ $question->id }})">
											Delete
										</x-button>
									</div>
								</td>
							</tr>
						@endforeach
						</tbody>
					</table>
				@else
					<div class="p-6 text-center text-gray-500 dark:text-gray-400">
						<p>No questions found for this template. Add your first question to get started.</p>
					</div>
				@endif
			</div>

			<!-- Create Question Modal -->
			<x-modal wire="showCreateQuestionModal">
				<x-slot:title>Add Question</x-slot:title>

				<div class="space-y-4">
					<div>
						<x-textarea
								label="Question Text"
								wire:model="questionText"
								placeholder="Enter question text" />
						@error('questionText') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
					</div>

					<div>
						<x-select.styled
								label="Question Type"
								wire:model="questionType"
								placeholder="Select question type"
								:request="route('enums.response-types')" />
					</div>

					<div>
						<x-select.styled
								label="Question Category"
								wire:model="questionCategory"
								placeholder="Select question category"
								:request="route('enums.question-categories')" />
					</div>

					<div>
						<x-checkbox
								label="Required Question"
								wire:model="isRequired" />
						@error('isRequired') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
					</div>

					<!-- Options for select, multiselect, radio, checkbox types -->
					<div
							x-data="{ show: false }"
							x-init="$watch('$wire.questionType', value => { show = ['select', 'multiselect', 'radio', 'checkbox'].includes(value) })">
						<div
								x-show="show"
								class="space-y-2">
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Options</label>

							<div class="flex space-x-2">
								<x-input
										wire:model="newOption"
										placeholder="Add an option"
										class="flex-1" />
								<x-button
										color="blue"
										wire:click="addOption">Add
								</x-button>
							</div>

							<div class="mt-2">
								@if(count($questionOptions) > 0)
									<ul class="space-y-2">
										@foreach($questionOptions as $index => $option)
											<li class="flex items-center justify-between bg-gray-100 dark:bg-dark-700 p-2 rounded">
												<span>{{ $option }}</span>
												<x-button
														color="red"
														size="sm"
														wire:click="removeOption({{ $index }})">
													<svg
															xmlns="http://www.w3.org/2000/svg"
															class="h-4 w-4"
															viewBox="0 0 20 20"
															fill="currentColor">
														<path
																fill-rule="evenodd"
																d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
																clip-rule="evenodd" />
													</svg>
												</x-button>
											</li>
										@endforeach
									</ul>
								@else
									<p class="text-sm text-gray-500 dark:text-gray-400">No options added yet.</p>
								@endif
							</div>

							@error('questionOptions') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
						</div>
					</div>
				</div>

				<x-slot:footer>
					<div class="flex justify-end space-x-2">
						<x-button
								color="gray"
								wire:click="$set('showCreateQuestionModal', false)">Cancel
						</x-button>
						<x-button
								color="blue"
								wire:click="createQuestion">Add Question
						</x-button>
					</div>
				</x-slot:footer>
			</x-modal>

			<!-- Edit Question Modal -->
			<x-modal wire="showEditQuestionModal">
				<x-slot:title>Edit Question</x-slot:title>

				<div class="space-y-4">
					<div>
						<x-textarea
								label="Question Text"
								wire:model="questionText"
								placeholder="Enter question text" />
						@error('questionText') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
					</div>

					<div>
						<x-select.styled
								label="Question Type"
								wire:model="questionType"
								placeholder="Select question type"
								:request="route('enums.response-types')" />
					</div>

					<div>
						<x-select.styled
								label="Question Category"
								wire:model="questionCategory"
								placeholder="Select question category"
								:request="route('enums.question-categories')" />
						@error('questionCategory') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
					</div>

					<div>
						<x-checkbox
								label="Required Question"
								wire:model="isRequired" />
						@error('isRequired') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
					</div>

					<!-- Options for select, multiselect, radio, checkbox types -->
					<div
							x-data="{ show: false }"
							x-init="$watch('$wire.questionType', value => { show = ['select', 'multiselect', 'radio', 'checkbox'].includes(value) })">
						<div
								x-show="show"
								class="space-y-2">
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Options</label>

							<div class="flex space-x-2">
								<x-input
										wire:model="newOption"
										placeholder="Add an option"
										class="flex-1" />
								<x-button
										color="blue"
										wire:click="addOption">Add
								</x-button>
							</div>

							<div class="mt-2">
								@if(count($questionOptions) > 0)
									<ul class="space-y-2">
										@foreach($questionOptions as $index => $option)
											<li class="flex items-center justify-between bg-gray-100 dark:bg-dark-700 p-2 rounded">
												<span>{{ $option }}</span>
												<x-button
														color="red"
														size="sm"
														wire:click="removeOption({{ $index }})">
													<svg
															xmlns="http://www.w3.org/2000/svg"
															class="h-4 w-4"
															viewBox="0 0 20 20"
															fill="currentColor">
														<path
																fill-rule="evenodd"
																d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
																clip-rule="evenodd" />
													</svg>
												</x-button>
											</li>
										@endforeach
									</ul>
								@else
									<p class="text-sm text-gray-500 dark:text-gray-400">No options added yet.</p>
								@endif
							</div>

							@error('questionOptions') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
						</div>
					</div>
				</div>

				<x-slot:footer>
					<div class="flex justify-end space-x-2">
						<x-button
								color="gray"
								wire:click="$set('showEditQuestionModal', false)">Cancel
						</x-button>
						<x-button
								color="blue"
								wire:click="updateQuestion">Update Question
						</x-button>
					</div>
				</x-slot:footer>
			</x-modal>

			<!-- Delete Question Confirmation Modal -->
			<x-modal wire="showDeleteQuestionModal">
				<x-slot:title>Delete Question</x-slot:title>

				<div class="space-y-4">
					<p class="text-gray-700 dark:text-gray-300">
						Are you sure you want to delete this question? This action cannot be undone.
					</p>
				</div>

				<x-slot:footer>
					<div class="flex justify-end space-x-2">
						<x-button
								color="gray"
								wire:click="$set('showDeleteQuestionModal', false)">Cancel
						</x-button>
						<x-button
								color="red"
								wire:click="deleteQuestion">Delete Question
						</x-button>
					</div>
				</x-slot:footer>
			</x-modal>
		</div>
	</div>
</div>
