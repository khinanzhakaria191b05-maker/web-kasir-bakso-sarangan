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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique();
            $table->foreignId('user_id')->constrained();          // kasir yang melayani
            $table->enum('order_type', ['dine_in', 'takeaway']);
            $table->string('table_number')->nullable();
            $table->text('note')->nullable();
            $table->unsignedInteger('total');
            $table->enum('payment_method', ['tunai', 'qris']);
            $table->unsignedInteger('cash_received')->nullable();
            $table->unsignedInteger('change_amount')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
