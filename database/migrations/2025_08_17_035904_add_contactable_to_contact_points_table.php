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
        Schema::table('contact_points', function (Blueprint $table) {
            // Remove the subject_id column
            $table->dropForeign(['subject_id']);
            $table->dropColumn('subject_id');

            // Add contactable_id and contactable_type columns for polymorphic relationships
            $table->unsignedBigInteger('contactable_id');
            $table->string('contactable_type');

            // Add index for the polymorphic relationship
            $table->index(['contactable_id', 'contactable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_points', function (Blueprint $table) {
            // Remove the polymorphic relationship columns
            $table->dropIndex(['contactable_id', 'contactable_type']);
            $table->dropColumn(['contactable_id', 'contactable_type']);

            // Add back the subject_id column
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
        });
    }
};
