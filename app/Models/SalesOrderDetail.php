<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrderDetail extends Model
{
    protected $fillable = [
        'sales_order_id',
        'item_id',
        'qty',
        'harga_modal_saat_transaksi',
        'harga_satuan_saat_transaksi',
        'diskon',
        'subtotal_netto'
    ];

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
