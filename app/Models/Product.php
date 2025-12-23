<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $table = "products";
    protected $fillable = [
        'title',
        'product_sku',
        'short_desc',
        'long_desc',
        'collection_id',
        'category_id',
        'sub_category_id',
        'image',
        'status',
        'slug',
        'product_type',
        'is_featured',
        'is_new_arrival',
        'is_bestseller',
        'meta_title',
        'meta_description',
        'meta_keyword',
        'deleted_at',
    ];

    public function collection()
    {
        return $this->belongsTo(Collection::class, 'collection_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    public function items()
    {
        return $this->hasMany(ProductItem::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

     public function getRentDurationAttribute()
    {
        return env('DEFAULT_RENT_DURATION', 30);
    }
}
