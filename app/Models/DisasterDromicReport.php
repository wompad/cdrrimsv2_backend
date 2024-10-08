<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisasterDromicReport extends Model
{
    use HasFactory;

    protected $table = 'tbl_disaster_reports';
    protected $primaryKey = 'uuid';

    protected $fillable = [
        'uuid',
        'incident_id',
        'report_name',
        'report_date',
        'as_of_time',
        'prepared_by',
        'recommended_by',
        'approved_by',
        'prepared_by_position',
        'recommended_by_position',
        'approved_by_position',
        'created_by'
    ];

    // Disable auto-incrementing ID since we're using UUID
    public $incrementing = false;

    // Define the primary key type as string for UUID
    protected $keyType = 'string';

}
