<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AllAffected extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'tbl_all_affected';

    protected $primaryKey = 'uuid';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = true;

    protected $fillable = [
        'uuid',
        'province_psgc_code',
        'municipality_psgc_code',
        'brgy_psgc_code',
        'totally_damaged',
        'partially_damaged',
        'disaster_report_uuid',
        'cost_asst_brgy',
        'affected_families',
        'affected_persons',
    ];
}
