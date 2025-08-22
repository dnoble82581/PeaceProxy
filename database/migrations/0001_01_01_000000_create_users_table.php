<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            // Tenant Relationship
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');

            // Authentication
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // Role and Position
            $table->string('permissions')->nullable(); // e.g., 'admin', 'user', 'superadmin', 'observer'
            $table->string('rank_or_title')->nullable(); // Officer rank or mental health job title

            // Identity / Credentials
            $table->string('badge_number')->nullable(); // for law enforcement
            $table->string('license_number')->nullable(); // for clinicians
            $table->string('department')->nullable(); // subunit of agency

            // Contact Details
            $table->string('phone')->nullable();
            $table->string('extension')->nullable();
            $table->string('alternate_email')->nullable();

            // Activity Tracking
            $table->timestamp('last_login_at')->nullable();
            $table->ipAddress('last_login_ip')->nullable();

            // Avatar and Preferences
            $table->string('avatar_path')->nullable();
            $table->string('locale')->default('en');
            $table->string('timezone')->default('America/Chicago');
            $table->boolean('dark_mode')->default(false);

            // Status
            $table->boolean('is_active')->default(true);

            // MFA / Security
            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
