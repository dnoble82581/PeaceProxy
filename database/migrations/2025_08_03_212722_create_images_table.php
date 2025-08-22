<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('imageable_id');
            $table->string('imageable_type');
            $table->foreignId('tenant_id');
            $table->foreignId('negotiation_id')->nullable();
            $table->string('path');
            $table->string('url');
            $table->string('type')->nullable();
            $table->string('original_filename')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->string('disk')->default('s3');
            $table->string('title')->nullable();
            $table->string('alt_text')->nullable();
            $table->unsignedInteger('order')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['imageable_id', 'imageable_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
