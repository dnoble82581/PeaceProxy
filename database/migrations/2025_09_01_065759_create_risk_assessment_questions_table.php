<?php

use App\Enums\Assessment\QuestionCategories;
use App\Enums\Assessment\QuestionResponseTypes;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('risk_assessment_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by_id');
            $table->foreignId('tenant_id');
            $table->foreignId('risk_assessment_id');
            $table->text('question');
            $table->enum('type', array_map(fn ($type) => $type->value, QuestionResponseTypes::cases()))->default(QuestionResponseTypes::text->value);
            $table->enum('category', array_map(fn ($category) => $category->value, \App\Enums\Assessment\QuestionCategories::cases()))->default(QuestionCategories::subject->value);
            $table->boolean('is_active');
            $table->timestamps();
        });

        Schema::create('risk_assessment_question_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id');
            $table->foreignId('tenant_id');
            $table->foreignId('risk_assessment_id');
            $table->foreignId('subject_id')->nullable();
            $table->foreignId('created_by_id');
            $table->text('text_response')->nullable();
            $table->integer('number_response')->nullable();
            $table->string('rating_string_response')->nullable();
            $table->string('rating_number_response')->nullable();
            $table->text('textarea_response')->nullable();
            $table->string('select_response')->nullable();
            $table->json('multiselect_response')->nullable();
            $table->json('checkbox_response')->nullable();
            $table->string('radio_response')->nullable();
            $table->date('date_response')->nullable();
            $table->time('time_response')->nullable();
            $table->dateTime('datetime_response')->nullable();
            $table->string('file_response')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risk_assessment_questions');
        Schema::dropIfExists('risk_assessment_responses');

    }
};
