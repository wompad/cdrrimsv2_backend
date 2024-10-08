<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuthUser;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AuthUserController extends Controller
{
    public function store(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'firstname'         => 'required|string|max:255',
            'lastname'          => 'required|string|max:255',
            'email_address'     => 'required|string|email|max:255|unique:auth_users,email_address',
            'username'          => 'required|string|max:255|unique:auth_users,username',
            'password'          => 'required|string|min:8',
            'user_type'         => 'required|in:Admin,User',
            'access_level'      => 'required|in:Region,Province,Municipal',
            'province_psgc'     => 'required|string|max:255',
            'municipality_psgc' => 'required|string|max:255',
            'is_activated'      => 'required|boolean',
            'is_dswd'           => 'required|boolean'
        ],[
            'province_psgc.required' => 'The province field is required',
            'municipality_psgc.required' => 'The municipality field is required'
        ]);

        // If validation fails, return a JSON response with errors
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Create a new user
        $user = AuthUser::create([
            'firstname'         => $request->firstname,
            'lastname'          => $request->lastname,
            'middlename'        => $request->middlename,
            'email_address'     => $request->email_address,
            'username'          => $request->username,
            'password'          => $request->password,
            'user_type'         => $request->user_type,
            'access_level'      => $request->access_level,
            'province_psgc'     => $request->province_psgc,
            'municipality_psgc' => $request->municipality_psgc,
            'is_activated'      => $request->is_activated,
            'is_dswd'           => $request->is_dswd
        ]);

        $token = $user->createToken('API Token')->plainTextToken;

        // Return a JSON response indicating success
        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user,
        ], 201);
    }

    public function getUsers()
    {
        // Fetch all users from the database
        $users = AuthUser::leftJoin('lib_provinces', 'auth_users.province_psgc', '=', 'lib_provinces.psgc_code')
        ->leftJoin('lib_municipalities', 'auth_users.municipality_psgc', '=', 'lib_municipalities.psgc_code')
        ->select('auth_users.*', 'lib_provinces.name as province_name', 'lib_municipalities.name as municipality_name')
        ->get();

        // Return the users in a JSON response
        return response()->json($users);
    }

    public function updateUser(Request $request, $user_id)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'firstname'         => 'required|string|max:255',
            'lastname'          => 'required|string|max:255',
            'user_type'         => 'required|in:Admin,User',
            'access_level'      => 'required|in:Region,Province,Municipal',
            'province_psgc'     => 'required|string|max:255',
            'municipality_psgc' => 'required|string|max:255',
            'is_activated'      => 'required|boolean',
            'is_dswd'           => 'required|boolean'
        ],[
            'province_psgc.required' => 'The province field is required',
            'municipality_psgc.required' => 'The municipality field is required'
        ]);

        // If validation fails, return a JSON response with errors
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = AuthUser::findOrFail($user_id);

        $user->update($request->only([
            'firstname',
            'lastname',
            'user_type',
            'access_level',
            'province_psgc',
            'municipality_psgc',
            'is_activated',
            'is_dswd'
        ]));

        return response()->json(['message' => 'User successfully updated', 'user' => $user], 201);
    }

    public function apitoken(Request $request){

        //return $request;

        // Assuming you have a user instance
        $user = AuthUser::findOrFail($request->id); // Replace with your user fetching logic

        // Create a token with a name
        $token = $user->createToken('CDRRIMS')->accessToken;

        // Optionally, return the token in response
        return response()->json(['token' => $token]);

    }
}
