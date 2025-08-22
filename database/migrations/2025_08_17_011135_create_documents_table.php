<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('negotiation_id')->nullable();
            $table->foreignId('tenant_id');
            // File details
            $table->string('name');
            $table->string('file_path'); // S3 key or path
            $table->string('file_type');
            $table->unsignedBigInteger('file_size');
            $table->string('storage_disk')->default('s3');

            // Polymorphic relationship
            $table->morphs('documentable');

            // Metadata & access control
            $table->string('category')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_private')->default(true);
            $table->json('tags')->nullable();

            // Auditing
            $table->foreignId('uploaded_by_id')->constrained('users');
            $table->boolean('encrypted')->default(false);
            $table->string('access_token')->nullable(); // For public/shared access
            $table->timestamp('presigned_url_expires_at')->nullable(); // Optional tracking

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
