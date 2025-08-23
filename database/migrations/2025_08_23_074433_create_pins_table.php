<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('pins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('user_id');     // who pinned (last pinner)
            $table->morphs('pinnable');                // pinnable_type, pinnable_id
            $table->timestamps();

            $table->unique(['tenant_id', 'pinnable_type', 'pinnable_id']); // one visible pin per item per tenant
            $table->index('tenant_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pins');
    }
};
