<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InsideEc;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class InsideEcController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'province_psgc_code'        => 'required',
            'municipality_psgc_code'    => ['required',
                Rule::exists('tbl_all_affected', 'municipality_psgc_code')
                ->where(function ($query) use ($request) {
                    $query->where('disaster_report_uuid', $request->disaster_report_uuid);
                })
            ],
            'brgy_located_ec_psgc_code' => 'required',
            'ec_uuid'                   => 'required',
            'ec_cum'                    => 'required|integer|min:0|max:1',
            'ec_now'                    => 'required|integer|min:0|max:1',
            'families_cum'              => 'required|integer|min:0',
            'families_now'              => 'required|integer|min:0',
            'persons_cum'               => 'required|integer|min:0',
            'persons_now'               => 'required|integer|min:0',
            'brgy_origin_psgc_codes'    => 'required',
            'disaster_report_uuid'      => 'required|uuid',
        ],[
            'province_psgc_code.required'        => 'Province is required',
            'municipality_psgc_code.required'    => 'Municipality is required',
            'municipality_psgc_code.exists'      => 'There are no reports on affected in this city/municipality for the given disaster report.',
            'brgy_located_ec_psgc_code.required' => 'Barangay located (EC) is required',
            'families_cum.required'              => 'Affected families cum field is required',
            'families_now.required'              => 'Affected families now field is required',
            'persons_cum.required'               => 'Affected persons cum field is required',
            'persons_now.required'               => 'Affected persons now field is required',
            'brgy_origin_psgc_codes.required'    => 'IDP origin is required',
            'ec_uuid.required'                   => 'Evacuation center is required'
        ]);

        $validator->after(function ($validator) use ($request) {
            // Custom validation logic
            if ($request->families_now > $request->families_cum) {
                $validator->errors()->add('families_now', 'Families Now should not be greater than Families Cumulative.');
            }

            if ($request->persons_now > $request->persons_cum) {
                $validator->errors()->add('persons_now', 'Persons Now should not be greater than Persons Cumulative.');
            }

            if ($request->persons_cum < $request->families_cum) {
                $validator->errors()->add('persons_cum', 'Persons Cumulative should not be less than Families Cumulative.');
            }

            if ($request->persons_now < $request->families_now) {
                $validator->errors()->add('persons_now', 'Persons Now should not be less than Families Now.');
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'message'=> 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Store the data
        $insideEc = InsideEc::create([
            'uuid'                       => Str::uuid(), // Generate a new UUID
            'province_psgc_code'         => $request->province_psgc_code,
            'municipality_psgc_code'     => $request->municipality_psgc_code,
            'brgy_located_ec_psgc_code'  => $request->brgy_located_ec_psgc_code,
            'ec_uuid'                    => $request->ec_uuid,
            'ec_cum'                     => $request->ec_cum ?? 0,
            'ec_now'                     => $request->ec_now ?? 0,
            'families_cum'               => $request->families_cum ?? 0,
            'families_now'               => $request->families_now ?? 0,
            'persons_cum'                => $request->persons_cum ?? 0,
            'persons_now'                => $request->persons_now ?? 0,
            'brgy_origin_psgc_codes'     => $request->brgy_origin_psgc_codes ?? '',
            'ec_status'                  => $request->ec_status ?? '',
            'ec_remarks'                 => $request->ec_remarks ?? '',
            'disaster_report_uuid'       => $request->disaster_report_uuid
        ]);

        // Return success response
        return response()->json([
            'message' => 'Data stored successfully!',
            'data' => $insideEc
        ], 201);
    }
    public function checkEC(Request $request)
    {
        $ec_uuid = $request->input('ec_uuid');
        $disaster_report_uuid = $request->input('disaster_report_uuid');

        $exists = InsideEc::where('ec_uuid', $ec_uuid)
                ->where('disaster_report_uuid', $disaster_report_uuid)
                ->exists();

        if ($exists) {
            return response()->json(['exists' => true, 'message' => 'EC UUID already exists for this disaster report.']);
        } else {
            return response()->json(['exists' => false, 'message' => 'EC UUID does not exist for this disaster report.']);
        }
    }
    public function getAllECReport($disasterReportUUID)
    {

        $sqlRegion = "SELECT SUM
                        ( t1.ec_cum ) ec_cum,
                        SUM ( t1.ec_now ) ec_now,
                        SUM ( t1.families_cum ) families_cum,
                        SUM ( t1.families_now ) families_now,
                        SUM ( t1.persons_cum ) persons_cum,
                        SUM ( t1.persons_now ) persons_now
                        FROM
                        tbl_inside_ec t1
                        LEFT JOIN lib_provinces t2 ON t1.province_psgc_code = t2.psgc_code
                        WHERE
                        t1.disaster_report_uuid = ?
                    ";

        $result['region'] = DB::select($sqlRegion, [$disasterReportUUID]);

        $sqlProvince = "SELECT
                        t1.province_psgc_code,
                        t2.name,
                        SUM ( t1.ec_cum ) ec_cum,
                        SUM ( t1.ec_now ) ec_now,
                        SUM ( t1.families_cum ) families_cum,
                        SUM ( t1.families_now ) families_now,
                        SUM ( t1.persons_cum ) persons_cum,
                        SUM ( t1.persons_now ) persons_now
                        FROM
                        tbl_inside_ec t1
                        LEFT JOIN lib_provinces t2 ON t1.province_psgc_code = t2.psgc_code
                        WHERE
                        t1.disaster_report_uuid = ?
                        GROUP BY
                        t1.province_psgc_code,
                        t2.name
                        ORDER BY
                        t1.province_psgc_code
                    ";

        $result['province'] = DB::select($sqlProvince, [$disasterReportUUID]);

        $sqlMunicipality = "SELECT
                            t1.province_psgc_code,
                            t1.municipality_psgc_code,
                            t2.name,
                            SUM ( t1.ec_cum ) ec_cum,
                            SUM ( t1.ec_now ) ec_now,
                            SUM ( t1.families_cum ) families_cum,
                            SUM ( t1.families_now ) families_now,
                            SUM ( t1.persons_cum ) persons_cum,
                            SUM ( t1.persons_now ) persons_now
                            FROM
                            tbl_inside_ec t1
                            LEFT JOIN lib_municipalities t2 ON t1.municipality_psgc_code = t2.psgc_code
                            WHERE
                            t1.disaster_report_uuid = ?
                            GROUP BY
                            t1.province_psgc_code,
                            t1.municipality_psgc_code,
                            t2.name
                            ORDER BY
                            t1.province_psgc_code,
                            t1.municipality_psgc_code
                        ";

        $result['municipality'] = DB::select($sqlMunicipality, [$disasterReportUUID]);

        $sqlBarangay = "SELECT
                            ROW_NUMBER( ) OVER ( PARTITION BY t1.ec_uuid ORDER BY t1.created_at ASC) AS ec_order,
                            ROW_NUMBER( ) OVER ( PARTITION BY t1.ec_uuid ORDER BY t1.created_at DESC) AS ec_dependents,
                            t1.uuid,
                            t1.province_psgc_code,
                            t1.municipality_psgc_code,
                            t1.brgy_located_ec_psgc_code,
                            t3.name,
                            t1.ec_uuid,
                            t2.evacuation_center_name,
                            t1.ec_cum,
                            t1.ec_now,
                            t1.families_cum,
                            t1.families_now,
                            t1.persons_cum,
                            t1.persons_now,
                            t1.ec_status,
                            t1.ec_remarks,
                            t1.brgy_origin_psgc_codes brgy_codes,
                            (SELECT
                            string_agg(name, ', ') brgy_names
                            FROM
                            lib_barangays
                            WHERE
                            psgc_code = ANY(string_to_array(t1.brgy_origin_psgc_codes, ',')))
                            FROM
                            tbl_inside_ec t1
                            LEFT JOIN tbl_evacuation_centers t2 ON t1.ec_uuid = t2.uuid
                            LEFT JOIN lib_barangays t3 ON t1.brgy_located_ec_psgc_code = t3.psgc_code
                            WHERE t1.disaster_report_uuid = ?
                            ORDER BY
                            t1.province_psgc_code,
                            t1.municipality_psgc_code,
                            t1.brgy_located_ec_psgc_code,
                            t1.ec_uuid,
                            t1.created_at
                        ";
        $result['barangay'] = DB::select($sqlBarangay, [$disasterReportUUID]);

        return response()->json($result, 201);

    }
    public function updateECDetails(Request $request, $uuid)
    {
        $validator = Validator::make($request->all(), [
            'ec_cum'                    => 'required|integer|min:0|max:1',
            'ec_now'                    => 'required|integer|min:0|max:1',
            'families_cum'              => 'required|integer|min:0',
            'families_now'              => 'required|integer|min:0',
            'persons_cum'               => 'required|integer|min:0',
            'persons_now'               => 'required|integer|min:0',
            'brgy_origin_psgc_codes'    => 'required'
        ],[
            'families_cum.required'              => 'Affected families cum field is required',
            'families_now.required'              => 'Affected families now field is required',
            'persons_cum.required'               => 'Affected persons cum field is required',
            'persons_now.required'               => 'Affected persons now field is required',
            'brgy_origin_psgc_codes.required'    => 'IDP origin is required'
        ]);

        $validator->after(function ($validator) use ($request) {
            // Custom validation logic
            if ($request->families_now > $request->families_cum) {
                $validator->errors()->add('families_now', 'Families Now should not be greater than Families Cumulative.');
            }

            if ($request->persons_now > $request->persons_cum) {
                $validator->errors()->add('persons_now', 'Persons Now should not be greater than Persons Cumulative.');
            }

            if ($request->persons_cum < $request->families_cum) {
                $validator->errors()->add('persons_cum', 'Persons Cumulative should not be less than Families Cumulative.');
            }

            if ($request->persons_now < $request->families_now) {
                $validator->errors()->add('persons_now', 'Persons Now should not be less than Families Now.');
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'message'=> 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        $insideEc = InsideEc::findOrFail($uuid);

        // Update the record with the new values from the request
        $insideEc->update([
            'ec_cum'                     => $request->ec_cum ?? 0,
            'ec_now'                     => $request->ec_now ?? 0,
            'families_cum'               => $request->families_cum ?? 0,
            'families_now'               => $request->families_now ?? 0,
            'persons_cum'                => $request->persons_cum ?? 0,
            'persons_now'                => $request->persons_now ?? 0,
            'brgy_origin_psgc_codes'     => $request->brgy_origin_psgc_codes ?? '',
            'ec_status'                  => $request->ec_status ?? '',
            'ec_remarks'                 => $request->ec_remarks ?? '',
        ]);

        return response()->json(['message' => 'Record updated successfully'], 200);

    }
    public function deleteEvacuationCenter($uuid)
    {
        $insideec = InsideEc::findOrFail($uuid);

        // Delete the evacuation center
        $insideec->delete();

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'Evacuation center data deleted successfully!'
        ], 200);
    }
}
