<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
$table->foreignId('tenant_id');
$table->foreignId('negotiation_id');
$table->foreignId('subject_id');
$table->string('title');
$table->timestamps();//
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
