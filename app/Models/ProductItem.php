<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductItem extends Model
{
    protected $fillable = [
        'product_type',
        'product_id',
        'color_id',
        'size_id',
        'base_price',
        'display_price',
        'image',
        'specification',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

}
