<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    /** @use HasFactory<\Database\Factories\CustomerFactory> */
    use HasFactory;

    protected $fillable = [
        'nama',
        'alamat',
        'no_telp',
    ];

    public function salesOrders()
    {
        return $this->hasMany(SalesOrder::class);
    }
}
