<?php

use App\Enums\DeliveryPlan\Status;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('delivery_plans', function (Blueprint $table) {
            $table->enum('status', array_map(
                fn ($case) => $case->value,
                Status::cases()
            ))->default(Status::in_progress->value)->change();
        });

    }
    public function down(): void
    {
        // Revert back to a plain string column
    }
};
