<?php

	use App\Enums\Assessment\QuestionCategories;
	use App\Enums\Assessment\QuestionResponseTypes;
	use App\Models\RiskAssessment;
	use App\Models\RiskAssessmentQuestion;
	use App\Models\Subject;
	use App\Services\RiskAssessment\RiskAssessmentCreationService;
	use App\Services\RiskAssessment\RiskAssessmentFetchingService;
	use App\Services\RiskAssessment\RiskAssessmentQuestionsCreationService;
	use App\Services\RiskAssessment\RiskAssessmentQuestionsFetchingService;
	use App\DTOs\RiskAssessment\RiskAssessmentDTO;
	use App\DTOs\RiskAssessment\RiskAssessmentQuestionsDTO;
	use Illuminate\Support\Facades\Auth;
	use Livewire\Attributes\Layout;
	use Livewire\Attributes\Validate;
	use Livewire\Volt\Component;

	new #[Layout('layouts.app')] class extends Component {
		// Risk Assessment properties
		public string $title = '';
		public ?int $subject_id = null;

		// Risk Assessment Question properties
		public ?int $selectedRiskAssessmentId = null;
		public string $question = '';
		public string $type = '';
		public string $category = '';

		// Validation rules
		public function rules()
		{
			return [
				'title' => 'required|string|min:3|max:255',
				'subject_id' => 'nullable|exists:subjects,id',
				'question' => 'required|string|min:5',
				'type' => 'required|string',
				'category' => 'required|string',
			];
		}

		// Get all risk assessments for the current tenant
		public function getRiskAssessments()
		{
			$riskAssessmentFetchingService = app(RiskAssessmentFetchingService::class);
			return $riskAssessmentFetchingService->getRiskAssessmentsByTenant(auth()->user()->tenant_id);
		}

		// Get questions for a specific risk assessment
		public function getQuestions($riskAssessmentId)
		{
			$questionsFetchingService = app(RiskAssessmentQuestionsFetchingService::class);
			// Use the repository pattern through the service
			return RiskAssessmentQuestion::where('risk_assessment_id', $riskAssessmentId)->get();
			// Note: Ideally, we would add a method to the RiskAssessmentQuestionsFetchingService
			// to get questions by risk_assessment_id, but for now we're using the model directly
		}

		// Create a new risk assessment
		public function createRiskAssessment()
		{
			$this->validate([
				'title' => 'required|string|min:3|max:255',
				'subject_id' => 'nullable|exists:subjects,id',
			]);

			$riskAssessmentDTO = new RiskAssessmentDTO(
				null,
				$this->title,
				auth()->user()->tenant_id,
				$this->subject_id
			);

			$riskAssessmentCreationService = app(RiskAssessmentCreationService::class);
			$riskAssessment = $riskAssessmentCreationService->createRiskAssessment($riskAssessmentDTO);

			$this->title = '';
			$this->subject_id = null;

			session()->flash('message', 'Risk Assessment created successfully!');
		}

		// Create a new question for a risk assessment
		public function createQuestion()
		{
			$this->validate([
				'selectedRiskAssessmentId' => 'required|exists:risk_assessments,id',
				'question' => 'required|string|min:5',
				'type' => 'required|string',
				'category' => 'required|string',
			]);

			$questionDTO = new RiskAssessmentQuestionsDTO(
				null,
				null, // negotiation_id
				auth()->user()->id, // created_by_id
				auth()->user()->tenant_id,
				$this->selectedRiskAssessmentId, // risk_assessment_id
				$this->question,
				$this->type,
				$this->category,
				true // is_active
			);

			$questionsCreationService = app(RiskAssessmentQuestionsCreationService::class);
			$question = $questionsCreationService->createQuestion($questionDTO);

			$this->question = '';
			$this->type = '';
			$this->category = '';

			session()->flash('message', 'Question added successfully!');
		}

		// Set the selected risk assessment for adding questions
		public function selectRiskAssessment($id)
		{
			$this->selectedRiskAssessmentId = $id;
		}

		// Get subjects for the dropdown
		public function getSubjects()
		{
			// Get subjects for the current tenant
			return Subject::where('tenant_id', auth()->user()->tenant_id)->get();
		}
	}
?>

<div class="py-12">
	<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
		<div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
			<h2 class="text-2xl font-bold mb-6">Risk Assessments</h2>

			<!-- Create Risk Assessment Form -->
			<div class="mb-10 p-6 bg-gray-50 rounded-lg">
				<h3 class="text-lg font-semibold mb-4">Create New Risk Assessment</h3>
				<form wire:submit.prevent="createRiskAssessment">
					<div class="mb-4">
						<label
								for="title"
								class="block text-sm font-medium text-gray-700">Title</label>
						<input
								type="text"
								id="title"
								wire:model="title"
								class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
						@error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
					</div>

					<div class="mb-4">
						<label
								for="subject_id"
								class="block text-sm font-medium text-gray-700">Subject (Optional)</label>
						<select
								id="subject_id"
								wire:model="subject_id"
								class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
							<option value="">Select a subject</option>
							@foreach($this->getSubjects() as $subject)
								<option value="{{ $subject->id }}">{{ $subject->name }}</option>
							@endforeach
						</select>
						@error('subject_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
					</div>

					<button
							type="submit"
							class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
						Create Risk Assessment
					</button>
				</form>
			</div>

			<!-- List of Risk Assessments -->
			<div>
				<h3 class="text-lg font-semibold mb-4">Your Risk Assessments</h3>

				@if(count($this->getRiskAssessments()) > 0)
					<div class="space-y-4">
						@foreach($this->getRiskAssessments() as $riskAssessment)
							<div class="border rounded-lg p-4">
								<div class="flex justify-between items-center mb-2">
									<h4 class="text-lg font-medium">{{ $riskAssessment->title }}</h4>
									<button
											wire:click="selectRiskAssessment({{ $riskAssessment->id }})"
											class="text-sm text-indigo-600 hover:text-indigo-900"
									>
										Add Question
									</button>
								</div>

								<!-- Questions for this risk assessment -->
								<div class="mt-4">
									<h5 class="text-md font-medium mb-2">Questions:</h5>
									@if(count($this->getQuestions($riskAssessment->id)) > 0)
										<ul class="list-disc pl-5 space-y-1">
											@foreach($this->getQuestions($riskAssessment->id) as $question)
												<li>
													<span class="font-medium">{{ $question->question }}</span>
													<span class="text-sm text-gray-500">({{ $question->type }})</span>
												</li>
											@endforeach
										</ul>
									@else
										<p class="text-sm text-gray-500">No questions added yet.</p>
									@endif
								</div>
							</div>
						@endforeach
					</div>
				@else
					<p class="text-gray-500">No risk assessments found. Create one above!</p>
				@endif
			</div>

			<!-- Add Question Form (shows when a risk assessment is selected) -->
			@if($selectedRiskAssessmentId)
				<div class="mt-10 p-6 bg-gray-50 rounded-lg">
					<h3 class="text-lg font-semibold mb-4">Add Question to Risk Assessment</h3>
					<form wire:submit.prevent="createQuestion">
						<div class="mb-4">
							<label
									for="question"
									class="block text-sm font-medium text-gray-700">Question</label>
							<textarea
									id="question"
									wire:model="question"
									rows="3"
									class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
							@error('question') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
						</div>

						<div class="mb-4">
							<label
									for="type"
									class="block text-sm font-medium text-gray-700">Response Type</label>
							<select
									id="type"
									wire:model="type"
									class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
								<option value="">Select a type</option>
								@foreach(App\Enums\Assessment\QuestionResponseTypes::cases() as $responseType)
									<option value="{{ $responseType->value }}">{{ $responseType->value }}</option>
								@endforeach
							</select>
							@error('type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
						</div>

						<div class="mb-4">
							<label
									for="category"
									class="block text-sm font-medium text-gray-700">Category</label>
							<select
									id="category"
									wire:model="category"
									class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
								<option value="">Select a category</option>
								@foreach(QuestionCategories::cases() as $questionCategory)
									<option value="{{ $questionCategory->value }}">{{ $questionCategory->value }}</option>
								@endforeach
							</select>
							@error('category') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
						</div>

						<div class="flex justify-between">
							<button
									type="submit"
									class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
								Add Question
							</button>

							<button
									type="button"
									wire:click="$set('selectedRiskAssessmentId', null)"
									class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
								Cancel
							</button>
						</div>
					</form>
				</div>
			@endif

			<!-- Flash Messages -->
			@if(session()->has('message'))
				<div class="mt-4 p-4 bg-green-100 text-green-700 rounded-md">
					{{ session('message') }}
				</div>
			@endif
		</div>
	</div>
</div>
