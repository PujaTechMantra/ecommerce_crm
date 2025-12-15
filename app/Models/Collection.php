<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    protected $fillable = [
        'name',
        'image',
        'slug',
        'status',
    ];
    
    public function categories()
    {
        return $this->hasMany(Category::class);
    }
}
