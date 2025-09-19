<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('delivery_planables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_plan_id')->constrained()->cascadeOnDelete();
            $table->morphs('planable'); // planable_type, planable_id
            $table->string('role')->nullable();   // e.g. 'subject', 'hostage', 'escort', 'drop_location'
            $table->string('notes', 1000)->nullable();
            $table->timestamps();

            $table->unique(['delivery_plan_id', 'planable_type', 'planable_id'], 'dp_planables_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_planables');
    }
};
