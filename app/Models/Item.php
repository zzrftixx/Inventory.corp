<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'category_id',
        'satuan',
        'harga_jual_default',
        'harga_beli_rata_rata',
        'stok_saat_ini',
        'batas_stok_minimum',
        'is_aluminium',
        'berat_profil_kg',
        'panjang_meter',
        'harga_dasar_aluminium_kg'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'item_supplier')
            ->withPivot('kode_barang_pabrik', 'harga_beli_terakhir')
            ->withTimestamps();
    }
}
