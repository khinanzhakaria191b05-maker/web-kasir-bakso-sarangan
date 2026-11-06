<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedInteger('subtotal')->default(0)->after('note');
            $table->enum('discount_type', ['nominal', 'percent'])->nullable()->after('subtotal');
            $table->decimal('discount_value', 10, 2)->default(0)->after('discount_type');
            $table->unsignedInteger('discount_amount')->default(0)->after('discount_value');
            $table->decimal('tax_rate', 5, 2)->default(0)->after('discount_amount');
            $table->unsignedInteger('tax_amount')->default(0)->after('tax_rate');
        });

        // Transaksi lama: subtotal = total (belum ada diskon/PPN)
        DB::table('orders')->update(['subtotal' => DB::raw('total')]);
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['subtotal', 'discount_type', 'discount_value', 'discount_amount', 'tax_rate', 'tax_amount']);
        });
    }
};
