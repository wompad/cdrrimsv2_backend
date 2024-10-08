<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LibMunicipalities;

class LibMunicipalitiesController extends Controller
{
    public function getmunicipalities()
    {
        // Query all items from the lib_provinces table
        $municipalities = LibMunicipalities::all();

        // Return the results as a JSON response
        return response()->json($municipalities);
    }

    public function getMunicipalitiesByProvince($provincePsgc)
    {
        // Fetch municipalities based on the given province_psgc
        $municipalities = LibMunicipalities::where('province_psgc_code', $provincePsgc)->get();

        // Return the data as JSON response
        return response()->json($municipalities);
    }
}
