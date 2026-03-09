<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsReceipt extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'no_surat_jalan_supplier',
        'tanggal_terima',
        'penerima_id',
        'catatan'
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function penerima()
    {
        return $this->belongsTo(User::class, 'penerima_id');
    }

    public function details()
    {
        return $this->hasMany(GoodsReceiptDetail::class);
    }
}
