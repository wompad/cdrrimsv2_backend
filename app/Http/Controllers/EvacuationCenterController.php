<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EvacuationCenter;
use App\Models\InsideEc;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\Paginator;

class EvacuationCenterController extends Controller
{
    /**
     * Store a newly created evacuation center in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'province_psgc_code'      => 'required|string|max:255',
            'municipality_psgc_code'  => 'required|string|max:255',
            'brgy_psgc_code'          => 'required|string|max:255',
            'evacuation_center_name'  => 'required|string|max:255',
            'description'             => 'nullable|string',
            'evacuation_center_type'  => 'required|string|max:255',
            'capacity'                => 'nullable|integer',
            'camp_manager_name'       => 'nullable|string',
            'camp_manager_contact'    => 'nullable|string',
        ],[
            'province_psgc_code.required'     => 'Province is required',
            'municipality_psgc_code.required' => 'Municipality is required',
            'brgy_psgc_code.required'         => 'Barangay is required'
        ]);

        $validated['evacuation_center_name'] = strtoupper($validated['evacuation_center_name']);

        // Create and save the EvacuationCenter
        $evacuationCenter = EvacuationCenter::create($validated);

        // Return a success response
        return response()->json([
            'message' => 'Evacuation center created successfully!',
            'data' => $evacuationCenter
        ], 201);
    }
    function paginateECs(Request $request){
        // Define the number of items per page
        $perPage = 10; // Adjust as needed
        $currentPage = $request->input('page', 1);

        // Build the query
        $query = DB::table('tbl_evacuation_centers as t1')
            ->leftJoin('lib_provinces as t2', 't1.province_psgc_code', '=', 't2.psgc_code')
            ->leftJoin('lib_municipalities as t3', 't1.municipality_psgc_code', '=', 't3.psgc_code')
            ->leftJoin('lib_barangays as t4', 't1.brgy_psgc_code', '=', 't4.psgc_code')
            ->select('t1.*', 't2.name as province_name', 't3.name as municipality_name', 't4.name as barangay_name');

        // Apply pagination
        $ecs = $query->paginate($perPage);

        // Return the formatted response
        return response()->json([
            'ecs' => $ecs->items(),          // The current items for the page
            'total' => $ecs->total(),          // The total number of items
            'current_page' => $ecs->currentPage(), // The current page number
            'last_page' => $ecs->lastPage(),   // The last page number
            'per_page' => $ecs->perPage(),     // The number of items per page
        ]);
    }

    function paginateECs2(Request $request)
    {
        // Get page size from request (default 10 per page)
        $perPage = $request->input('per_page', 10);
        $currentPage = $request->input('page', 1);
        $searchTerm = $request->input('search', '');

        // Query evacuation centers with pagination and searching
        $evacuationCenters = DB::table('tbl_evacuation_centers as t1')
                ->leftJoin('lib_provinces as t2', 't1.province_psgc_code', '=', 't2.psgc_code')
                ->leftJoin('lib_municipalities as t3', 't1.municipality_psgc_code', '=', 't3.psgc_code')
                ->leftJoin('lib_barangays as t4', 't1.brgy_psgc_code', '=', 't4.psgc_code')
                ->select(
                    't1.uuid',
                    't1.province_psgc_code',
                    't1.municipality_psgc_code',
                    't1.brgy_psgc_code',
                    't1.evacuation_center_name',
                    't1.description',
                    't1.evacuation_center_type',
                    't1.capacity',
                    't1.camp_manager_name',
                    't1.camp_manager_contact',
                    't1.created_at',
                    't1.updated_at',
                    't2.name as province_name',
                    't3.name as municipality_name',
                    't4.name as barangay_name'
                )
                ->where(DB::raw('LOWER(t2.name)'), 'like', '%' . strtolower($searchTerm) . '%') // Search in province name
                ->orWhere(DB::raw('LOWER(t1.evacuation_center_name)'), 'like', '%' . strtolower($searchTerm) . '%') // Search in evacuation center name
                ->orWhere(DB::raw('LOWER(t3.name)'), 'like', '%' . strtolower($searchTerm) . '%') // Search in municipality name
                ->orWhere(DB::raw('LOWER(t4.name)'), 'like', '%' . strtolower($searchTerm) . '%') // Search in barangay name
                ->orderBy('t1.brgy_psgc_code', 'asc')
                ->orderBy('t1.evacuation_center_name', 'asc')
                ->paginate($perPage, ['*'], 'page', $currentPage
        );

        // Return paginated data as JSON
        return response()->json($evacuationCenters);
    }
    public function getAllECs()
    {
        // Get all evacuation centers
        $sqlECs = "SELECT
                    t1.*,
                    t2.NAME province_name,
                    t3.NAME municipality_name,
                    t4.NAME barangay_name
                    FROM
                    tbl_evacuation_centers t1
                    LEFT JOIN lib_provinces t2 ON t1.province_psgc_code = t2.psgc_code
                    LEFT JOIN lib_municipalities t3 ON t1.municipality_psgc_code = t3.psgc_code
                    LEFT JOIN lib_barangays t4 ON t1.brgy_psgc_code = t4.psgc_code
                    ORDER BY
                    t1.brgy_psgc_code ASC, t1.evacuation_center_name ASC
                ";

        $result = DB::select($sqlECs);

        // Return the data as a JSON response
        return response()->json($result, 201);
    }
    public function getEvacuationCentersByBarangay($brgy_psgc_code)
    {
        // Validate input (optional)
        if (empty($brgy_psgc_code)) {
            return response()->json(['error' => 'Barangay code is required'], 400);
        }
        // Query to get all evacuation centers by barangay code
        $evacuationCenters = EvacuationCenter::where('brgy_psgc_code', $brgy_psgc_code)->get();

        // Return response
        return response()->json($evacuationCenters, 200);
    }
    public function updateEC(Request $request, $uuid)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'province_psgc_code'      => 'required|string|max:255',
            'municipality_psgc_code'  => 'required|string|max:255',
            'brgy_psgc_code'          => 'required|string|max:255',
            'evacuation_center_name'  => 'required|string|max:255',
            'description'             => 'nullable|string',
            'evacuation_center_type'  => 'required|string|max:255',
            'capacity'                => 'nullable|integer',
            'camp_manager_name'       => 'nullable|string',
            'camp_manager_contact'    => 'nullable|string',
        ],[
            'province_psgc_code.required'     => 'Province is required',
            'municipality_psgc_code.required' => 'Municipality is required',
            'brgy_psgc_code.required'         => 'Barangay is required'
        ]);

        // If validation fails, return a JSON response with errors
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $request->merge([
            'evacuation_center_name' => strtoupper($request->evacuation_center_name)
        ]);

        $ec = EvacuationCenter::findOrFail($uuid);

        $ec->update($request->only([
            'evacuation_center_name',
            'description',
            'evacuation_center_type',
            'capacity',
            'camp_manager_name',
            'camp_manager_contact'
        ]));

        return response()->json(['message' => 'Evacuation center successfuly updated', 'ec' => $ec], 201);
    }
    public function checkExistingEC($uuid)
    {
        $ec = EvacuationCenter::findOrFail($uuid);

        // Check if there are any related entries in the InsideEc model
        $insideEcExists = InsideEc::where('ec_uuid', $uuid)->exists();

        $status = false;

        if ($insideEcExists) {
            $status = true;// 400 Bad Request
        }else{
            $status = false;
        }

        return response()->json([
            'status' => $status,
        ], 200);
    }
    public function deleteEC($uuid)
    {
        $ec = EvacuationCenter::findOrFail($uuid);

        // Check if there are any related entries in the InsideEc model
        $insideEcExists = InsideEc::where('ec_uuid', $uuid)->exists();

        if ($insideEcExists) {
            // Return an error response indicating that the evacuation center cannot be deleted
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete evacuation center. Referenced found in DROMIC Report.'
            ], 400); // 400 Bad Request
        }

        // Delete the evacuation center
        $ec->delete();

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'Evacuation center deleted successfully!'
        ], 200);
    }
}
