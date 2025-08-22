<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('contact_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_point_id')->constrained('contact_points')->cascadeOnDelete();
            $table->string('address1');
            $table->string('address2')->nullable();
            $table->string('city')->nullable();
            $table->string('region')->nullable();
            $table->string('postal_code', 5)->nullable();
            $table->char('country_iso', 2);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();

            $table->index(['country_iso', 'postal_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_addresses');
    }
};
