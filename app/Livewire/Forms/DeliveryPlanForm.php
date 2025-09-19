<?php

namespace App\Livewire\Forms;

use App\Enums\DeliveryPlan\ContingencyStatus;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;

class DeliveryPlanForm extends Form
{
    #[Validate(['required', 'integer'])]
    public $tenant_id = null;

    #[Validate(['required', 'integer'])]
    public $negotiation_id = '';

    #[Validate(['required', 'integer'])]
    public $created_by = '';

    #[Validate(['required', 'integer'])]
    public $updated_by = '';

    #[Validate(['required'])]
    public $title = '';

    #[Validate(['nullable'])]
    public $summary = '';

    #[Validate(['nullable'])]
    public $category = '';

    #[Validate(['required'])]
    public $status = '';

    #[Validate(['nullable', 'date'])]
    public $scheduled_at = null;

    #[Validate(['nullable', 'string'])]
    public $window_starts_at = null;

    #[Validate(['nullable', 'string'])]
    public $window_ends_at = null;

    #[Validate(['nullable'])]
    public $location_name = '';

    #[Validate(['nullable'])]
    public $location_address = '';

    #[Validate(['nullable'])]
    public $geo = [];

    #[Validate(['nullable'])]
    public $route = [];

    #[Validate(['nullable'])]
    public $instructions = [];

    #[Validate(['nullable'])]
    public $constraints = [];

    #[Validate(['nullable'])]
    public $contingencies = [];

    #[Validate(['nullable'])]
    public $risk_assessment = [];

    #[Validate(['nullable'])]
    public $signals = [];

    public $role = '';

    public $notes = '';

    public function rules(): array
    {
        return [
            //            'demand_id'                         => 'required|integer|exists:demands,id',

//            'status' => ['required', Rule::enum(Status::class)],

            'contingencies' => 'nullable|array',
            'contingencies.*.id' => 'required|string',
            'contingencies.*.title' => 'required|string|max:120',
            'contingencies.*.triggers' => 'nullable|string|max:2000',
            'contingencies.*.actions' => 'nullable|string|max:5000',
            'contingencies.*._resources_input' => 'nullable|string', // temp comma string
            'contingencies.*.resources' => 'nullable|array',
            'contingencies.*.resources.*' => 'nullable|string|max:120',
            'contingencies.*.comms' => 'nullable|string|max:1000',
            'contingencies.*.criteria' => 'nullable|string|max:1000',
            'contingencies.*.notes' => 'nullable|string|max:4000',
            'contingencies.*.status' => ['required', Rule::enum(ContingencyStatus::class)],
        ];
    }
}
