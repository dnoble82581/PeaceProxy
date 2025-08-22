<?php

use App\Enums\Subject\MoodLevels;
use App\Enums\Subject\SubjectNegotiationStatuses;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();

            // Basic information
            $table->string('name');
            $table->json('alias')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable();

            // Physical characteristics
            $table->string('height')->nullable();
            $table->string('weight')->nullable();
            $table->string('hair_color')->nullable();
            $table->string('eye_color')->nullable();
            $table->text('identifying_features')->nullable();

            // Background information
            $table->string('occupation')->nullable();
            $table->string('employer')->nullable();
            $table->text('mental_health_history')->nullable();
            $table->text('criminal_history')->nullable();
            $table->text('substance_abuse_history')->nullable();
            $table->text('known_weapons')->nullable();

            // Risk assessment
            $table->json('risk_factors')->nullable();

            // Notes and status
            $table->text('notes')->nullable();
            $table->integer('current_mood')->default(MoodLevels::Neutral->value);
            $table->string('status')->default(SubjectNegotiationStatuses::unknown->value);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
