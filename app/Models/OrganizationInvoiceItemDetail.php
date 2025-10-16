<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationInvoiceItemDetail extends Model
{
    protected $table = 'organization_invoice_item_details';
    
    protected $fillable = [
        'invoice_item_id', 'order_id', 'date', 'day_amount'
    ];

    public function invoice_item(){
        return $this->belongsTo(OrganizationInvoiceItem::class, 'invoice_item_id', 'id');
    }
    public function order(){
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}
