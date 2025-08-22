<?php

use App\Enums\General\RiskLevels;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('warnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id');
            $table->foreignId('created_by_id');
            $table->enum('risk_level', array_map(
                fn ($risk_level) => $risk_level->value,
                RiskLevels::cases()
            ))->default(RiskLevels::low->value);
            $table->string('warning_type');
            $table->string('warning');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warnings');
    }
};
