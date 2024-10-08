<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles; // Add this for Spatie roles
use Laravel\Sanctum\HasApiTokens;

class AuthUser extends Model
{
    use HasRoles;
    use HasFactory;
    use HasApiTokens;

    protected $table = 'auth_users';

    protected $primaryKey = 'id';

    public $timestamps = true;

    // protected $guard_name = 'web';

    protected $fillable = [
        'firstname',
        'lastname',
        'middlename',
        'email_address',
        'username',
        'password',
        'user_type',
        'is_activated',
        'province_psgc',
        'municipality_psgc',
        'access_level',
        'is_dswd',
        'reset_token'
    ];

    // Specify hidden attributes (e.g., password, reset_token)
    protected $hidden = [
        'password',
        'reset_token',
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }
}
