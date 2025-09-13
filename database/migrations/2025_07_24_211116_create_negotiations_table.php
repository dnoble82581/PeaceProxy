<?php

use App\Enums\Negotiation\NegotiationStatuses;
use App\Enums\Negotiation\NegotiationTypes;
use App\Enums\Subject\SubjectNegotiationRoles;
use App\Enums\User\UserNegotiationStatuses;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('negotiations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->string('title')->unique();
            $table->text('summary')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('ended_at')->nullable();
            $table->string('location')->nullable();
            $table->string('location_address')->nullable();
            $table->string('location_city')->nullable();
            $table->string('location_state')->nullable();
            $table->integer('location_zip')->nullable();

            $table->enum('status', array_map(
                fn ($status) => $status->value,
                NegotiationStatuses::cases()
            ))->default(NegotiationStatuses::active->value);

            $table->enum('type', array_map(
                fn ($status) => $status->value,
                NegotiationTypes::cases()
            ))->default(NegotiationTypes::hostage->value);

            $table->text('initial_complaint')->nullable();
            $table->text('negotiation_strategy')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->json('tags')->nullable();
            $table->timestamps();
        });

        Schema::create('negotiation_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('negotiation_id');
            $table->foreignId('user_id');
            $table->string('role')->nullable();
            $table->enum('status', array_map(
                fn ($status) => $status->value,
                UserNegotiationStatuses::cases()
            ))->default(UserNegotiationStatuses::active->value);
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamp('left_at')->nullable();
            $table->timestamps();
        });

        Schema::create('negotiation_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('negotiation_id') ->references('id')->on('negotiations')
                ->onDelete('cascade');
            $table->foreignId('subject_id');
            $table->enum('role', array_map(fn ($status) => $status->value, SubjectNegotiationRoles::cases()))
                ->default(SubjectNegotiationRoles::secondary->value);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('negotiation_subjects');
        Schema::dropIfExists('negotiation_users');
        Schema::dropIfExists('negotiations');
    }
};
