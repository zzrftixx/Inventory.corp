<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Widen decimal columns to prevent "Out of range" errors
     * for large Rupiah values (especially aluminium items).
     * decimal(20,2) supports values up to 999,999,999,999,999,999.99
     */
    public function up(): void
    {
        Schema::table('purchase_order_details', function (Blueprint $table) {
            $table->decimal('harga_beli_satuan', 20, 2)->change();
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->decimal('total_amount_po', 20, 2)->change();
        });

        // Also widen items.harga_beli_rata_rata to prevent future overflow
        Schema::table('items', function (Blueprint $table) {
            $table->decimal('harga_beli_rata_rata', 20, 2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_order_details', function (Blueprint $table) {
            $table->decimal('harga_beli_satuan', 15, 2)->change();
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->decimal('total_amount_po', 15, 2)->change();
        });

        Schema::table('items', function (Blueprint $table) {
            $table->decimal('harga_beli_rata_rata', 15, 2)->default(0)->change();
        });
    }
};
