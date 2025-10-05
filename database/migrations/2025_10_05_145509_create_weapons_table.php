<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('weapons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->string('category', 30)->index();   // handgun, rifle, knife, etc.
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->string('caliber')->nullable();
            $table->string('status', 20)->default('reported')->index(); // reported|confirmed|recovered|rumored
            $table->string('source')->nullable(); // caller, officer, cctv, etc.
            $table->unsignedTinyInteger('threat_level')->nullable(); // 1–5
            $table->timestamp('last_seen_at')->nullable()->index();
            $table->foreignId('reported_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('description')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['tenant_id', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weapons');
    }
};
