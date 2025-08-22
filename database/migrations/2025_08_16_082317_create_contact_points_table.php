<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('contact_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->enum('kind', ['email', 'phone', 'address']);
            $table->string('label')->nullable(); // home | work | billing
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->integer('priority')->default(0);
            $table->timestamps();
            $table->index(['subject_id', 'kind']);
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('contact_points');
    }
};
