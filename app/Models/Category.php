<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes; 
    
    protected $table = "categories";
    protected $fillable = [
        'title',
        'image',
        'collection_id',
        'slug'
    ];

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }
    
    public function subcategories()
    {
        return $this->hasMany(SubCategory::class);
    }
    

}
