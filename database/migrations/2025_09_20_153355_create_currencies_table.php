<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id(); // id SERIAL PRIMARY KEY
            $table->string('code', 3)->unique(); // VARCHAR(3) UNIQUE NOT NULL
            $table->string('name', 50); // VARCHAR(50) NOT NULL
            $table->decimal('exchange_rate', 20, 10); // DECIMAL(20,10) NOT NULL
            $table->decimal('surcharge_percentage', 5, 2); // DECIMAL(5,2) NOT NULL
            $table->decimal('discount_percentage', 5, 2)->default(0); // DECIMAL(5,2) DEFAULT 0
            $table->timestamp('last_updated')->nullable(); // TIMESTAMP NULLABLE
            $table->timestamps(0); // created_at and updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
