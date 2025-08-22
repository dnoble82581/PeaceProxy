<?php

use App\Enums\General\Genders;
use App\Enums\General\RiskLevels;
use App\Enums\Hostage\HostageInjuryStatus;
use App\Enums\Hostage\HostageStatus;
use App\Enums\Hostage\HostageSubjectRelation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('hostages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id');
            $table->foreignId('negotiation_id');
            $table->string('name');
            $table->string('age')->nullable();
            $table->enum('gender', array_column(Genders::cases(), 'value'))->nullable();
            $table->enum('relation_to_subject', array_column(HostageSubjectRelation::cases(), 'value'))->nullable();
            $table->enum('risk_level', array_column(RiskLevels::cases(), 'value'))->nullable();
            $table->string('location')->nullable();
            $table->enum('status', array_column(HostageStatus::cases(), 'value'))->nullable();
            $table->dateTime('last_seen_at')->nullable();
            $table->dateTime('freed_at')->nullable();
            $table->dateTime('deceased_at')->nullable();
            $table->boolean('is_primary_hostage')->default(false);
            $table->enum(
                'injury_status',
                array_column(HostageInjuryStatus::cases(), 'value')
            )->nullable();
            $table->json('risk_factors')->nullable();
            $table->foreignId('created_by');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hostages');
    }
};
