<?php

use App\Enums\Objective\Priority;
use App\Enums\Objective\Status;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('objectives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id');
            $table->foreignId('negotiation_id');
            $table->foreignId('created_by_id');
            $table->boolean('is_pinned')->default(false);
            $table->enum('priority', array_map(
                fn ($priority) => $priority->value,
                Priority::cases()
            ))->default(Priority::low->value);
            $table->enum('status', array_map(
                fn ($status) => $status->value,
                Status::cases()
            ))->default(Status::pending->value);
            $table->string('objective');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('objectives');
    }
};
