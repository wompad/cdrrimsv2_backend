<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DromicReport extends Model
{
    use HasFactory;

        // Set the table name
        protected $table = 'tbl_dromic';
        protected $primaryKey = 'uuid';

        public $timestamps = true;

        // Specify which fields can be mass-assigned
        protected $fillable = [
            'uuid',
            'incident_name',
            'incident_date',
            'created_by',
        ];

        // Disable auto-incrementing ID since we're using UUID
        public $incrementing = false;

        // Define the primary key type as string for UUID
        protected $keyType = 'string';

}
