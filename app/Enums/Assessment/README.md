# Assessment Enums

This directory contains enums related to assessments and questionnaires in the PeaceProxy application.

## QuestionCategories

The `QuestionCategories` enum defines the possible categories that assessment questions can be classified into.

### Available Categories

- `subject`: Questions related to subject risk assessment
- `tactical`: Questions related to tactical risk assessment
- `operational`: Questions related to operational risk assessment
- `strategic`: Questions related to strategic risk assessment
- `environmental`: Questions related to environmental risk assessment
- `team`: Questions related to team risk assessment
- `communication`: Questions related to communication risk assessment
- `general`: General risk assessment questions

### Usage Examples

#### Importing the Enum

```php
use App\Enums\Assessment\QuestionCategories;
```

#### Using Enum Values

```php
// In a model
protected $casts = [
    'category' => QuestionCategories::class,
];

// In a migration
$table->string('category')->default(QuestionCategories::general->value);

// In a controller or service
public function createQuestion(array $data)
{
    $category = QuestionCategories::from($data['category']);
    
    // Use the category
    if ($category === QuestionCategories::subject) {
        // Handle subject-specific logic
    }
    
    // Get user-friendly label
    $label = $category->label();
}
```

#### Listing All Available Categories

```php
// Get all cases
$categories = QuestionCategories::cases();

// Create a select list with labels
$selectOptions = [];
foreach (QuestionCategories::cases() as $category) {
    $selectOptions[$category->value] = $category->label();
}
```

### Validation

When validating input that should match one of these categories:

```php
use Illuminate\Validation\Rules\Enum;

$request->validate([
    'category' => ['required', new Enum(QuestionCategories::class)],
]);
```

## QuestionResponseTypes

The `QuestionResponseTypes` enum defines the possible types of responses that can be collected for questions in assessments.

### Available Types

- `text`: Simple text input (single line)
- `number`: Numeric input
- `rating`: Rating scale (e.g., 1-5 stars)
- `textarea`: Multi-line text input
- `select`: Single-selection dropdown
- `multiselect`: Multiple-selection dropdown
- `checkbox`: Checkbox options
- `radio`: Radio button options
- `date`: Date picker
- `time`: Time picker
- `datetime`: Date and time picker
- `file`: File upload

### Usage Examples

#### Importing the Enum

```php
use App\Enums\Assessment\QuestionResponseTypes;
```

#### Using Enum Values

```php
// In a model
protected $casts = [
    'response_type' => QuestionResponseTypes::class,
];

// In a migration
$table->string('response_type')->default(QuestionResponseTypes::text->value);

// In a controller or service
public function createQuestion(array $data)
{
    $responseType = QuestionResponseTypes::from($data['response_type']);
    
    // Use the response type
    if ($responseType === QuestionResponseTypes::rating) {
        // Handle rating-specific logic
    }
    
    // Get user-friendly label
    $label = $responseType->label();
}
```

#### Listing All Available Types

```php
// Get all cases
$types = QuestionResponseTypes::cases();

// Create a select list with labels
$selectOptions = [];
foreach (QuestionResponseTypes::cases() as $type) {
    $selectOptions[$type->value] = $type->label();
}
```

### Validation

When validating input that should match one of these types:

```php
use Illuminate\Validation\Rules\Enum;

$request->validate([
    'response_type' => ['required', new Enum(QuestionResponseTypes::class)],
]);
```