<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InsideEc extends Model
{
    use HasFactory;

    protected $table = 'tbl_inside_ec';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = true;

    protected $fillable = [
        'uuid',
        'province_psgc_code',
        'municipality_psgc_code',
        'municipality_located_ec_psgc_code',
        'brgy_located_ec_psgc_code',
        'ec_uuid',
        'ec_cum',
        'ec_now',
        'families_cum',
        'families_now',
        'persons_cum',
        'persons_now',
        'brgy_origin_psgc_codes',
        'ec_status',
        'ec_remarks',
        'disaster_report_uuid',
    ];
}
