<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class OrganizationDiscount extends Model
{
    use HasFactory;

    protected $table = 'organization_discounts';

    protected $fillable = [
        'organization_id',
        'discount_percentage',
        'discount_is_positive',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'discount_percentage' => 'decimal:2',
        'discount_is_positive' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * 🔹 Relationship with Organization
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

}
