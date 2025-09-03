<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id');
            $table->foreignId('assessment_template_id');
            $table->integer('score')->default(0);
            $table->foreignId('negotiation_id');
            $table->foreignId('subject_id');
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->string('title');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
