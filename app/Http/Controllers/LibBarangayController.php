<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LibBarangays;
use Illuminate\Support\Facades\DB;

class LibBarangayController extends Controller
{
    public function getbrgys()
    {
        // Query all items from the lib_provinces table
        $barangays = LibBarangays::all();

        // Return the results as a JSON response
        return response()->json($barangays);
    }

    public function getBarangaysByMunicipality($municipalityPsgc)
    {
        // Fetch municipalities based on the given municipality_psgc_code
        $barangays = LibBarangays::where('municipality_psgc_code', $municipalityPsgc)->get();

        // Return the data as JSON response
        return response()->json($barangays);
    }
    public function getBarangaysByProvince($provincePsgc)
    {
        // Fetch municipalities based on the given province_psgc
        $barangays = DB::table('lib_barangays as t1')
            ->leftJoin('lib_municipalities as t2', 't1.municipality_psgc_code', '=', 't2.psgc_code')
            ->select('t1.psgc_code', 't1.name as brgy_name', 't2.name as municipality_name')
            ->where('t1.province_psgc_code', $provincePsgc)
            ->get();

        // Return the data as JSON response
        return response()->json($barangays);
    }
}
