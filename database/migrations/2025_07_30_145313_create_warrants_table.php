<?php

use App\Enums\Warrant\BondType;
use App\Enums\Warrant\WarrantStatus;
use App\Enums\Warrant\WarrantType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('warrants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id');
            $table->foreignId('subject_id');
            $table->enum('type', array_column(WarrantType::cases(), 'value'))->default(WarrantType::unknown->value);
            $table->enum(
                'status',
                array_column(WarrantStatus::cases(), 'value')
            )->default(WarrantStatus::active->value);
            $table->string('jurisdiction')->nullable();
            $table->string('court_name')->nullable();
            $table->text('offense_description')->nullable();
            $table->string('status_code')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->decimal('bond_amount', 15, 2)->nullable();
            $table->enum('bond_type', array_column(BondType::cases(), 'value'))->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warrants');
    }
};
