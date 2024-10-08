<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FNFIAssistance extends Model
{
    use HasFactory;

    // Define the table name (optional if following Laravel's naming convention)
    protected $table = 'tbl_fnfi_assistance';
    protected $primaryKey = 'uuid';

    // Use UUIDs instead of auto-incrementing IDs
    public $incrementing = false;
    protected $keyType = 'uuid';

    // Specify the attributes that are mass assignable
    protected $fillable = [
        'uuid',
        'disaster_report_uuid',
        'fnfi_uuid',
        'fnfi_cost',
        'fnfi_quantity',
        'augmentation_date',
        'province_psgc_code',
        'municipality_psgc_code'
    ];

    // Automatically handle created_at and updated_at timestamps
    public $timestamps = true;

}
