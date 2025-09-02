<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_template_questions', function (Blueprint $table) {
            $table->id();
$table->text('question');
$table->string('question_type');
$table->string('question_category');
$table->timestamps();//
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_template_questions');
    }
};
