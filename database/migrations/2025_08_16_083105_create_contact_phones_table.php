<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('contact_phones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_point_id')->constrained('contact_points')->cascadeOnDelete();
            $table->string('e164', 20); // +14155550123
            $table->string('ext', 10)->nullable();
            $table->char('country_iso', 2)->nullable();
            $table->timestamps();

            $table->index('e164');
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('contact_phones');
    }
};
