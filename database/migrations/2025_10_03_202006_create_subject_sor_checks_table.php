<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('subject_sor_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id');
            $table->foreignId('negotiation_id');
            $table->string('status')->default('pending');
            $table->string('result_count')->nullable();
            $table->json('matches')->nullable();
            $table->text('error')->nullable();
            $table->timestamp('checked_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'negotiation_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subject_sor_checks');
    }
};
