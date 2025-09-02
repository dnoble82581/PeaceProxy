<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_templates', function (Blueprint $table) {
            $table->id();
$table->string('title');
$table->foreignId('tenant_id');
$table->text('description')->nullable();
$table->timestamps();//
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_templates');
    }
};
