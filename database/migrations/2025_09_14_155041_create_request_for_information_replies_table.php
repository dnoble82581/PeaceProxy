<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('request_for_information_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_for_information_id');
            $table->foreignId('tenant_id');
            $table->foreignId('user_id');
            $table->text('body');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_for_information_replies');
    }
};
