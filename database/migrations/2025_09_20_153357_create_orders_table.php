<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id(); // Auto-increment primary key (id)
            $table->unsignedBigInteger('currency_id'); // foreign key to currencies table
            $table->decimal('exchange_rate', 20, 10)->default(0.00);
            $table->decimal('surcharge_percentage', 4, 3)->default(0.00);
            $table->decimal('discount_percentage', 4, 3)->default(0.00);
            $table->decimal('foreign_amount', 20, 2)->default(0.00);
            $table->decimal('zar_amount', 20, 2)->default(0.00);
            $table->decimal('surcharge_amount', 20, 2)->default(0.00);
            $table->timestamps(0); // created_at and updated_at

            // Foreign key constraint
            $table->foreign('currency_id')->references('id')->on('currencies');

            // Index
            $table->index('currency_id', 'idx_orders_currency_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
