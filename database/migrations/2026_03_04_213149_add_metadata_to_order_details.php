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
        Schema::table('sales_order_details', function (Blueprint $table) {
            if (!Schema::hasColumn('sales_order_details', 'metadata_kalkulasi')) {
                $table->json('metadata_kalkulasi')->nullable()->after('subtotal_netto');
            }
        });
        Schema::table('purchase_order_details', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_order_details', 'metadata_kalkulasi')) {
                $table->json('metadata_kalkulasi')->nullable()->after('harga_beli_satuan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_order_details', function (Blueprint $table) {
            if (Schema::hasColumn('sales_order_details', 'metadata_kalkulasi')) {
                $table->dropColumn('metadata_kalkulasi');
            }
        });
        Schema::table('purchase_order_details', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_order_details', 'metadata_kalkulasi')) {
                $table->dropColumn('metadata_kalkulasi');
            }
        });
    }
};
