<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\FNFIAssistance;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class FNFIAssistanceController extends Controller
{
    public function validateForm(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'disaster_report_uuid'   => 'required|uuid',
            'province_psgc_code'     => 'required|string',
            'municipality_psgc_code' => ['required',
                Rule::exists('tbl_all_affected', 'municipality_psgc_code')
                ->where(function ($query) use ($request) {
                    $query->where('disaster_report_uuid', $request->disaster_report_uuid);
                })
            ],
            'fnfi_uuid'              => 'required|uuid',
            'fnfi_cost'              => 'required|numeric|min:1',
            'fnfi_quantity'          => 'required|integer|min:1',
            'augmentation_date'      => 'required|date|before_or_equal:today',
        ],[
            'province_psgc_code.required'       => 'The province field is required',
            'municipality_psgc_code.required'   => 'The municipality field is required',
            'municipality_psgc_code.exists'     => 'There are no reports on affected in this city/municipality for the given disaster report.',
            'fnfi_uuid.required'                => 'FNFI item is required',
            'fnfi_cost.required'                => 'Cost is required',
            'fnfi_quantity.required'            => 'Quantity is required',
            'augmentation_date.required'        => 'Augmentation date is required',
            'augmentation_date.before_or_equal' => 'Future dates are not allowed for the augmentation date.'
        ]
        );

        // Handle validation failure
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }
        // Return a response, e.g., redirect or JSON
        return response()->json([
            'message' => 'Record is valid.'
        ], 201);

    }
    public function batchSave(Request $request)
    {

        $records = $request->all();

        // Iterate over each record to prepare for insertion
        foreach ($records as &$record) {
            // Automatically generate UUID if not provided
            $record['uuid'] = Str::uuid();

            FNFIAssistance::create([
                'uuid'                      => $record['uuid'],
                'disaster_report_uuid'      => $record['disaster_report_uuid'],
                'province_psgc_code'        => $record['province_psgc_code'],
                'municipality_psgc_code'    => $record['municipality_psgc_code'],
                'fnfi_uuid'                 => $record['fnfi_uuid'],
                'fnfi_cost'                 => $record['fnfi_cost'],
                'fnfi_quantity'             => $record['fnfi_quantity'],
                'augmentation_date'         => $record['augmentation_date']
            ]);

        }

        // Return a success response
        return response()->json([
            'message' => 'Data successfully saved.',
            'data' => $records,
        ], 201);
    }
    public function getFNFIAssistance($disasterReportUuid)
    {
        $results['municipality'] = DB::table('tbl_fnfi_assistance as t1')
            ->select(
                't1.uuid',
                't1.municipality_psgc_code',
                't1.province_psgc_code',
                't2.name',
                't3.fnfi_name',
                't1.fnfi_cost',
                't1.fnfi_quantity',
                't1.augmentation_date',
                DB::raw('(t1.fnfi_cost * t1.fnfi_quantity) AS amount'),
                DB::raw("'m' AS identifier")
            )
            ->leftJoin('lib_municipalities as t2', 't1.municipality_psgc_code', '=', 't2.psgc_code')
            ->leftJoin('tbl_fnfis as t3', 't1.fnfi_uuid', '=', 't3.uuid')
            ->where('t1.disaster_report_uuid', $disasterReportUuid)
            ->orderBy('t1.municipality_psgc_code', 'asc')
            ->orderBy('t1.augmentation_date', 'desc') // Order by augmentation_date descending
            ->get();

        $results['province'] = DB::table('tbl_fnfi_assistance as t1')
            ->select(
                't1.province_psgc_code',
                't2.name',
                DB::raw('SUM(t1.fnfi_cost * t1.fnfi_quantity) AS amount'),
                DB::raw("'p' AS identifier")
            )
            ->leftJoin('lib_provinces as t2', 't1.province_psgc_code', '=', 't2.psgc_code')
            ->where('t1.disaster_report_uuid', $disasterReportUuid)
            ->groupBy('t1.province_psgc_code', 't2.name')
            ->orderBy('t1.province_psgc_code')
            ->get();

        return response()->json($results);
    }
    public function destroy($uuid)
    {
        // Find the record by uuid
        $fnfiassistance = FNFIAssistance::where('uuid', $uuid)->first();

        // Delete the record
        $fnfiassistance->delete();

        // Return a success response
        return response()->json(['message' => 'Record deleted successfully']);
    }
    public function getFNFIAssistanceReport($disasterReportUuid){

        $results['items'] = DB::table(DB::raw('(
                SELECT DISTINCT
                    t1.fnfi_uuid,
                    t2.fnfi_name
                FROM
                    tbl_fnfi_assistance as t1
                    LEFT JOIN tbl_fnfis as t2 ON t1.fnfi_uuid = t2.uuid
                WHERE
                    t1.disaster_report_uuid = ?
            ) as distinct_items'))
            ->select('*')
            ->orderByRaw("CASE
                WHEN lower(distinct_items.fnfi_name) = lower('Family Food Pack') THEN 1
                WHEN lower(distinct_items.fnfi_name) ILIKE lower('%kit%') THEN 2
                ELSE 3
            END", [$disasterReportUuid])
            ->get();

        $results['provinces'] =  DB::table('tbl_fnfi_assistance as t1')
            ->leftJoin('tbl_fnfis as t2', 't1.fnfi_uuid', '=', 't2.uuid')
            ->leftJoin('lib_provinces as t3', 't1.province_psgc_code', '=', 't3.psgc_code')
            ->select(
                't1.province_psgc_code',
                't3.name',
                't1.fnfi_uuid',
                't2.fnfi_name',
                DB::raw('SUM(t1.fnfi_quantity) as fnfi_quantity'),
                DB::raw('SUM(t1.fnfi_quantity * t1.fnfi_cost) as total_amount')
            )
            ->where('t1.disaster_report_uuid', $disasterReportUuid)
            ->groupBy(
                't1.province_psgc_code',
                't3.name',
                't1.fnfi_uuid',
                't2.fnfi_name'
            )
            ->get();

        $results['asst_provinces'] = DB::table('tbl_fnfi_assistance as t1')
            ->select(
                't1.province_psgc_code',
                't2.name',
                DB::raw('SUM(t1.fnfi_cost * t1.fnfi_quantity) AS amount'),
                DB::raw("'p' AS identifier")
            )
            ->leftJoin('lib_provinces as t2', 't1.province_psgc_code', '=', 't2.psgc_code')
            ->where('t1.disaster_report_uuid', $disasterReportUuid)
            ->groupBy('t1.province_psgc_code', 't2.name')
            ->orderBy('t1.province_psgc_code')
            ->get();

        $results['asst_municipalities'] = DB::table('tbl_fnfi_assistance as t1')
            ->distinct()
            ->select('t1.municipality_psgc_code', 't2.name', 't1.province_psgc_code')
            ->leftJoin('lib_municipalities as t2', 't1.municipality_psgc_code', '=', 't2.psgc_code')
            ->where('t1.disaster_report_uuid', '=', $disasterReportUuid)
            ->get();

        $results['municipalities'] = DB::table('tbl_fnfi_assistance as t1')
            ->leftJoin('tbl_fnfis as t2', 't1.fnfi_uuid', '=', 't2.uuid')
            ->leftJoin('lib_municipalities as t3', 't1.municipality_psgc_code', '=', 't3.psgc_code')
            ->select(
                't1.province_psgc_code',
                't1.municipality_psgc_code',
                't3.name',
                't1.fnfi_uuid',
                't2.fnfi_name',
                DB::raw('SUM(t1.fnfi_quantity) as fnfi_quantity'),
                DB::raw('SUM(t1.fnfi_quantity * t1.fnfi_cost) as total_amount')
            )
            ->where('t1.disaster_report_uuid', '=', $disasterReportUuid)
            ->groupBy(
                't1.province_psgc_code',
                't1.municipality_psgc_code',
                't3.name',
                't1.fnfi_uuid',
                't2.fnfi_name'
            )
            ->get();

        $results['region'] = DB::table('tbl_fnfi_assistance as t1')
            ->leftJoin('tbl_fnfis as t2', 't1.fnfi_uuid', '=', 't2.uuid')
            ->leftJoin('lib_provinces as t3', 't1.province_psgc_code', '=', 't3.psgc_code')
            ->select(
                't1.fnfi_uuid',
                't2.fnfi_name',
                DB::raw('SUM(t1.fnfi_quantity) as fnfi_quantity'),
                DB::raw('SUM(t1.fnfi_quantity * t1.fnfi_cost) as total_amount')
            )
            ->where('t1.disaster_report_uuid', $disasterReportUuid) // Use a dynamic value for UUID
            ->groupBy('t1.fnfi_uuid', 't2.fnfi_name')
            ->get();

        return response()->json($results);
    }
}
