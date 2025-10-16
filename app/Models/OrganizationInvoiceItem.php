<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationInvoiceItem extends Model
{
    protected $table = 'organization_invoice_items';

    protected $fillable = [
        'user_id', 'invoice_id', 'total_day', 'total_price'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function invoice(){
        return $this->belongsTo(OrganizationInvoice::class, 'invoice_id', 'id');
    }
    
    public function details()
    {
        return $this->hasMany(OrganizationInvoiceItemDetail::class, 'invoice_item_id');
    }
}
