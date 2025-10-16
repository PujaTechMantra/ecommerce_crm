<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrgInvoiceMerchantNumber extends Model
{
    use HasFactory;

    protected $table = 'org_invoice_merchant_numbers';

    protected $fillable = [
        'organization_id',
        'invoice_id',
        'merchantTxnNo',
        'redirect_url',
        'secureHash',
        'tranCtx',
        'amount',
    ];

    /**
     * If you want to link with OrganizationInvoice (optional)
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }
}
