<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('mood_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id');
            $table->foreignId('logged_by_id');
            $table->foreignId('tenant_id');
            $table->foreignId('negotiation_id');
            $table->unsignedTinyInteger('mood_level');
            $table->string('context')->nullable();
            $table->json('meta_data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mood_logs');
    }
};
