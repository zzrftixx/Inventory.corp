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
            $table->json('metadata_kalkulasi')->nullable()->after('subtotal_netto');
        });
        Schema::table('purchase_order_details', function (Blueprint $table) {
            $table->json('metadata_kalkulasi')->nullable()->after('subtotal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_order_details', function (Blueprint $table) {
            $table->dropColumn('metadata_kalkulasi');
        });
        Schema::table('purchase_order_details', function (Blueprint $table) {
            $table->dropColumn('metadata_kalkulasi');
        });
    }
};
