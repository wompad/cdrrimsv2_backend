<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DromicReport;
use Illuminate\Support\Str; // For generating UUID
use Illuminate\Support\Facades\DB;
use Validator;

class DromicReportController extends Controller
{
    /**
     * Store a new DROMIC report.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'incident_name'  => 'required|string|max:255',
            'incident_date'  => 'required|date|before_or_equal:today',
            'created_by'     => 'required|exists:auth_users,id', // Assuming created_by references the 'users' table
        ],[
            'incident_date.before_or_equal'=> 'Future dates are not allowed for the incident date.'
        ]);

        // If validation fails, return errors
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $dromicReport = DromicReport::create([
            'uuid'           => Str::uuid(),
            'incident_name'  => $request->incident_name,
            'incident_date'  => $request->incident_date,
            'created_by'     => $request->created_by
        ]);

        // Return success response
        return response()->json([
            'success' => true,
            'message' => 'DROMIC Incident Report successfully created',
            'data' => $dromicReport
        ], 201);
    }

    public function getIncidents($user_id)
    {
        // Fetch all users from the database
        $incidents = DromicReport::select(
            'tbl_dromic.*',
            'auth_users.username',
            DB::raw('COUNT(tbl_disaster_reports.uuid) as total_reports')
        )
        ->join('auth_users', 'tbl_dromic.created_by', '=', 'auth_users.id')
        ->leftJoin('tbl_disaster_reports', 'tbl_dromic.uuid', '=', 'tbl_disaster_reports.incident_id')
        ->where('tbl_dromic.created_by', $user_id)
        ->groupBy('tbl_dromic.uuid', 'auth_users.username')
        ->orderBy('tbl_dromic.incident_date', 'DESC')
        ->get();

        // Return the users in a JSON response
        return response()->json($incidents);
    }

    public function updateIncident(Request $request, $incident_id)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'incident_name'  => 'required|string|max:255',
            'incident_date'  => 'required|date'
        ]);

        // If validation fails, return a JSON response with errors
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $dromicReport = DromicReport::where('tbl_dromic.uuid', $incident_id)->firstOrFail();

        $dromicReport->update($request->only([
            'incident_name',
            'incident_date'
        ]));

        return response()->json(['message' => 'Incident successfully updated', 'dromicReport' => $dromicReport], 201);
    }

    public function getLatestDromicReport($incident_id){

        // Fetch the latest report associated to the incident_id
        $latest_report = DB::table('tbl_disaster_reports')
        ->leftJoin('tbl_dromic', 'tbl_disaster_reports.incident_id', '=', 'tbl_dromic.uuid')
        ->where('tbl_disaster_reports.incident_id', $incident_id)
        ->orderBy('tbl_disaster_reports.created_at', 'DESC')
        ->select('tbl_disaster_reports.*', 'tbl_dromic.incident_date')
        ->first();

        // Return the users in a JSON response
        return response()->json($latest_report);

    }

    public function getReportbyDisasterUUID($uuid){

        // Fetch the latest report associated to the incident_id
        $latest_report = DB::table('tbl_disaster_reports')
        ->leftJoin('tbl_dromic', 'tbl_disaster_reports.incident_id', '=', 'tbl_dromic.uuid')
        ->where('tbl_disaster_reports.uuid', $uuid)
        ->orderBy('tbl_disaster_reports.created_at', 'DESC')
        ->select('tbl_disaster_reports.*', 'tbl_dromic.incident_date')
        ->first();

        // Return the users in a JSON response
        return response()->json($latest_report);

    }

    public function getAllReports($incident_id){
        // Fetch the latest report associated to the incident_id
        $all_reports = DB::table('tbl_disaster_reports')
        ->leftJoin('tbl_dromic', 'tbl_disaster_reports.incident_id', '=', 'tbl_dromic.uuid')
        ->where('tbl_disaster_reports.incident_id', $incident_id)
        ->orderBy('tbl_disaster_reports.created_at', 'DESC')
        ->select('tbl_disaster_reports.*', 'tbl_dromic.incident_date')
        ->get();

        // Return the users in a JSON response
        return response()->json($all_reports);
    }

    public function destroy($uuid){
        // Find the record by uuid
        $dromicreport = DromicReport::where('uuid', $uuid)->first();

        // Delete the record
        $dromicreport->delete();

        // Return a success response
        return response()->json(['message' => 'Incident deleted successfully']);
    }

}
