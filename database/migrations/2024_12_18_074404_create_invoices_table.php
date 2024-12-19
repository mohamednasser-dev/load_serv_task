<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number');
            $table->foreignId('customer_id')->references('id')->on('customers')->onDelete('restrict');
            $table->double('amount')->default(0);
            $table->enum('payment_status',['paid','not_paid'])->default('not_paid');
            $table->enum('status',['pending','on_progress','shipped','delivered','rejected','canceled_by_user','canceled_by_admin'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
