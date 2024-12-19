<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoice_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->references('id')->on('products')->restrictOnDelete()->restrictOnUpdate();
            $table->foreignId('invoice_id')->references('id')->on('invoices')->cascadeOnDelete()->cascadeOnUpdate();
            $table->double('price')->default(0);
            $table->bigInteger('quantity')->default(1);
            $table->double('total')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_products');
    }
};
