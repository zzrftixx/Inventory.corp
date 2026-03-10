<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsReceiptDetail extends Model
{
    protected $fillable = [
        'goods_receipt_id',
        'purchase_order_detail_id',
        'item_id',
        'qty_diterima',
        'kondisi'
    ];

    public function goodsReceipt()
    {
        return $this->belongsTo(GoodsReceipt::class);
    }

    public function purchaseOrderDetail()
    {
        return $this->belongsTo(PurchaseOrderDetail::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
