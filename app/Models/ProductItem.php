<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_type',
        'product_id',
        'color_id',
        'size_id',
        'item_code',
        'base_price',
        'display_price',
        'image',
        'status',
        'specification',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_item_id');
    }

}
