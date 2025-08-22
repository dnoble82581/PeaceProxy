<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('calls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id');
            $table->foreignId('subject_id');
            $table->foreignId('negotiation_id');
            $table->string('call_number')->nullable();
            $table->string('call_recording_url')->nullable();
            $table->dateTime('start_time')->nullable();
            $table->string('recording_size')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->time('duration')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calls');
    }
};
