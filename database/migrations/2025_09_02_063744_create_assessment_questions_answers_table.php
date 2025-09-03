<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('assessment_questions_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id');
            $table->foreignId('assessment_template_question_id');
            $table->json('answer');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_questions_answers');
    }
};
