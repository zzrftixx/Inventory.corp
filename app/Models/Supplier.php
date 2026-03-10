<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model
{
    /** @use HasFactory<\Database\Factories\SupplierFactory> */
    use HasFactory;

    protected $fillable = [
        'nama_supplier',
        'kontak',
    ];

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_supplier')
            ->withPivot('kode_barang_pabrik', 'harga_beli_terakhir')
            ->withTimestamps();
    }
}
