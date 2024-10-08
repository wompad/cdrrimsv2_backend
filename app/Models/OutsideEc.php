<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutsideEc extends Model
{
    // Specify the table name if it does not follow Laravel's naming conventions
    protected $table = 'tbl_outside_ec';

    // Specify the primary key if it is not an auto-incrementing integer
    protected $primaryKey = 'uuid';

    // Set the primary key type
    protected $keyType = 'string';

    // Disable auto-incrementing for the UUID primary key
    public $incrementing = false;

    // Allow mass assignment for these attributes
    protected $fillable = [
        'uuid',
        'disaster_report_uuid',
        'host_province_psgc_code',
        'host_municipality_psgc_code',
        'host_brgy_psgc_code',
        'aff_families_cum',
        'aff_families_now',
        'aff_persons_cum',
        'aff_persons_now',
        'origin_province_psgc_code',
        'origin_municipality_psgc_code',
        'origin_brgy_psgc_code'
    ];

    // If you want to use timestamps in your table
    public $timestamps = true;
}

