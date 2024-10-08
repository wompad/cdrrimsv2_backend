<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\AuthUser;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        // Validate login credentials
        $credentials = $request->validate([
            'username_or_email' => ['required', 'string'],
            'password'          => ['required'],
        ]);

        // Attempt to authenticate the user
        $user = AuthUser::where('email_address', $credentials['username_or_email'])
                    ->orWhere('username', $credentials['username_or_email'])
                    ->first();

        // Check if user exists and the password is correct
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Assign roles and permissions based on user type
        // if ($user->user_type === 'Admin') {

        //     if (!$user->hasRole('Admin')) {
        //         $user->assignRole('Admin');
        //     }

        //     // Assign permissions to admin
        //     $user->givePermissionTo(['edit', 'view']);

        // } elseif ($user->user_type === 'User') {
        //     if (!$user->hasRole('User')) {
        //         $user->assignRole('User');
        //     }

        //     // Assign permissions to regular user
        //     $user->givePermissionTo('view');
        // }

        // Return user data and token to Vue.js frontend
        return response()->json([
            'user'          => $user,
            //'roles'         => $user->getRoleNames(), // Optional: Return role names
            //'permissions'   => $user->getAllPermissions()->pluck('name'), // Optional: Return permission names
        ]);
    }

    // Logout API (Revoke token)
    public function logout(Request $request)
    {
        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
