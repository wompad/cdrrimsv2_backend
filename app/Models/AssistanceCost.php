<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class AssistanceCost extends Model
{
    use HasFactory;

    protected $table = 'tbl_assistance_cost';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = true;

    protected $fillable = [
        'uuid',
        'disaster_report_uuid',
        'province_psgc_code',
        'municipality_psgc_code',
        'lgu_assistance',
        'ngo_assistance',
        'other_go_assistance'
    ];
}
