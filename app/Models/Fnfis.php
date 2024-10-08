<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fnfis extends Model
{
    use HasFactory;

    protected $table = 'tbl_fnfis';

    protected $primaryKey = 'uuid';

    public $timestamps = true;

    protected $fillable = [
        'uuid',
        'fnfi_name',
        'description',
        'item_price',
        'fnfi_type'
    ];

    // Disable auto-incrementing ID since we're using UUID
    public $incrementing = false;

    // Define the primary key type as string for UUID
    protected $keyType = 'string';
}
