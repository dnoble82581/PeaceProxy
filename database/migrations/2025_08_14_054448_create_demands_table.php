<?php

use App\Enums\Demand\DemandCategories;
use App\Enums\Demand\DemandStatuses;
use App\Enums\General\Channels;
use App\Enums\General\RiskLevels;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('demands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('negotiation_id');
            $table->foreignId('subject_id');
            $table->foreignId('tenant_id');
            $table->foreignId('created_by_id');

            $table->string('title');
            $table->text('content');
            $table->date('deadline_date')->nullable();
            $table->time('deadline_time')->nullable();
            $table->enum('category', array_map(
                fn ($category) => $category->value,
                DemandCategories::cases()
            ))->default(DemandCategories::substantive->value);
            $table->enum('status', array_map(
                fn ($status) => $status->value,
                DemandStatuses::cases()
            ))->default(DemandStatuses::pending->value);
            $table->enum('priority_level', array_map(
                fn ($riskLevel) => $riskLevel->value,
                RiskLevels::cases()
            ))->default(RiskLevels::low->value);
            $table->timestamp('communicated_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->enum(
                'channel',
                array_map(fn ($channel) => $channel->value, Channels::cases())
            )->default(Channels::phone->value);
            //            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('negotiation_id')->references('id')->on('negotiations')->cascadeOnDelete();
            $table->foreign('created_by_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('subject_id')->references('id')->on('subjects')->cascadeOnDelete();
            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demands');
    }
};
