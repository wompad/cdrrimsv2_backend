<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LibProvince;

class LibProvincesController extends Controller
{
    public function getprovinces()
    {
        // Query all items from the lib_provinces table
        $provinces = LibProvince::all();

        // Return the results as a JSON response
        return response()->json($provinces);
    }
}
