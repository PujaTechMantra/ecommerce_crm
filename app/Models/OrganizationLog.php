<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationLog extends Model
{
    protected $table = 'organization_logs';
    protected $fillable = [
        'organization_id', 'updated_by', 'trigger_type', 'old_data', 'new_data'
    ];
}
