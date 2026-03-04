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
        Schema::table('items', function (Blueprint $table) {
            $table->boolean('is_aluminium')->default(false)->after('satuan');
            $table->decimal('berat_profil_kg', 8, 3)->nullable()->after('is_aluminium'); // 3 angka di belakang koma untuk akurasi berat
            $table->decimal('panjang_meter', 4, 2)->nullable()->after('berat_profil_kg');
            $table->decimal('harga_dasar_aluminium_kg', 15, 2)->nullable()->after('panjang_meter');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['is_aluminium', 'berat_profil_kg', 'panjang_meter', 'harga_dasar_aluminium_kg']);
        });
    }
};
