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
        // 1. Add moving average cost to items
        Schema::table('items', function (Blueprint $table) {
            $table->decimal('harga_beli_rata_rata', 15, 2)->default(0)->after('harga_jual_default');
        });

        // 2. Add purchase price to PO details
        Schema::table('purchase_order_details', function (Blueprint $table) {
            $table->decimal('harga_beli_satuan', 15, 2)->default(0)->after('qty_butuh');
            // We might also add subtotal here, but let's keep it simple and just let controller calculate
        });

        // 3. Add COGS (Cost of Goods Sold) / Modal to SO details
        Schema::table('sales_order_details', function (Blueprint $table) {
            $table->decimal('harga_modal_saat_transaksi', 15, 2)->default(0)->after('qty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('harga_beli_rata_rata');
        });

        Schema::table('purchase_order_details', function (Blueprint $table) {
            $table->dropColumn('harga_beli_satuan');
        });

        Schema::table('sales_order_details', function (Blueprint $table) {
            $table->dropColumn('harga_modal_saat_transaksi');
        });
    }
};
