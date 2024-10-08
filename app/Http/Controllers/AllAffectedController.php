<?php

namespace App\Http\Controllers;

use App\Models\AllAffected;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AllAffectedController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Initial validation
        $validator = Validator::make($request->all(), [
            'province_psgc_code'     => 'required|string',
            'municipality_psgc_code' => 'required|string',
            'brgy_psgc_code'         => ['required','string',
                // Custom validation rule to check if brgy_psgc_code and disaster_report_uuid combination already exists
                Rule::unique('tbl_damage_per_brgy')->where(function ($query) use ($request) {
                    return $query->where('disaster_report_uuid', $request->disaster_report_uuid);
                }),
            ],
            'affected_families'      => 'required|integer|min:0',
            'affected_persons'       => 'required|integer|min:0',
            'totally_damaged'        => 'nullable|integer|min:0',
            'partially_damaged'      => 'nullable|integer|min:0',
            'cost_asst_brgy'         => 'nullable|numeric|min:0',
            'disaster_report_uuid'   => 'required|uuid',
        ]);

        // After validation: Check if affected_persons is lower than affected_families
        $validator->after(function ($validator) use ($request) {
            if ($request->affected_persons < $request->affected_families) {
                $validator->errors()->add('affected_persons', 'Must not be lower than the affected families');
            }

            // Check if the sum of totally_damaged and partially_damaged is greater than affected_families
            $totalDamaged = $request->totally_damaged + $request->partially_damaged;
            if ($totalDamaged > $request->affected_families) {
                $validator->errors()->add('affected_families', 'Must not be lower than the sum of totally and partially damaged houses');
            }
        });

        // Handle validation failure
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $data['uuid'] = \Illuminate\Support\Str::uuid();

        // Create a new DamagePerBrgy record
        $damagePerBrgy = AllAffected::create($data);

        // Return a response, e.g., redirect or JSON
        return response()->json([
            'message' => 'Record successfully created.',
            'data' => $damagePerBrgy,
        ], 201);
    }

    public function validateData(Request $request){
        // Initial validation
        $validator = Validator::make($request->all(), [
            'province_psgc_code'     => 'required|string',
            'municipality_psgc_code' => 'required|string',
            'brgy_psgc_code'         => ['required','string',
                // Custom validation rule to check if brgy_psgc_code and disaster_report_uuid combination already exists
                Rule::unique('tbl_all_affected')->where(function ($query) use ($request) {
                    return $query->where('disaster_report_uuid', $request->disaster_report_uuid);
                }),
            ],
            'affected_families'      => 'required|integer|min:1',
            'affected_persons'       => 'required|integer|min:1',
            'totally_damaged'        => 'nullable|integer|min:0',
            'partially_damaged'      => 'nullable|integer|min:0',
            'cost_asst_brgy'         => 'nullable|numeric|min:0',
            'disaster_report_uuid'   => 'required|uuid',
        ],[
            'province_psgc_code.required'       => 'The province field is required',
            'municipality_psgc_code.required'   => 'The municipality field is required',
            'brgy_psgc_code.required'           => 'The barangay field is required',
            'brgy_psgc_code.unique'             => 'Barangay already exists for the given disaster report.',
        ]
        );

        // After validation: Check if affected_persons is lower than affected_families
        $validator->after(function ($validator) use ($request) {
            if ($request->affected_persons < $request->affected_families) {
                $validator->errors()->add('affected_persons', 'Must not be lower than the affected families');
            }

            // Check if the sum of totally_damaged and partially_damaged is greater than affected_families
            $totalDamaged = $request->totally_damaged + $request->partially_damaged;
            if ($totalDamaged > $request->affected_families) {
                $validator->errors()->add('affected_families', 'Must not be lower than the sum of totally and partially damaged houses');
                $validator->errors()->add('totally_damaged', 'Must not be higher than the affected families');
                $validator->errors()->add('partially_damaged', 'Must not be higher than the affected families');
            }
        });

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

    public function updateAffected(Request $request){
        // Initial validation
        $validator = Validator::make($request->all(),
        [
            'affected_families'      => 'required|integer|min:1',
            'affected_persons'       => 'required|integer|min:1',
            'totally_damaged'        => 'nullable|integer|min:0',
            'partially_damaged'      => 'nullable|integer|min:0',
            'cost_asst_brgy'         => 'nullable|numeric|min:0',
        ]
        );

        // After validation: Check if affected_persons is lower than affected_families
        $validator->after(function ($validator) use ($request) {
            if ($request->affected_persons < $request->affected_families) {
                $validator->errors()->add('affected_persons', 'Must not be lower than the affected families');
            }

            // Check if the sum of totally_damaged and partially_damaged is greater than affected_families
            $totalDamaged = $request->totally_damaged + $request->partially_damaged;
            if ($totalDamaged > $request->affected_families) {
                $validator->errors()->add('affected_families', 'Must not be lower than the sum of totally and partially damaged houses');
                $validator->errors()->add('totally_damaged', 'Must not be higher than the affected families');
                $validator->errors()->add('partially_damaged', 'Must not be higher than the affected families');
            }
        });

        // Handle validation failure
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::table('tbl_all_affected')
            ->where('brgy_psgc_code', $request->brgy_psgc_code)
            ->where('disaster_report_uuid', $request->disaster_report_uuid)
            ->update([
                'affected_families' => $request->affected_families,
                'affected_persons' => $request->affected_persons,
                'totally_damaged' => $request->totally_damaged,
                'partially_damaged' => $request->partially_damaged,
                'cost_asst_brgy' => $request->cost_asst_brgy
            ]);

        // Return a response, e.g., redirect or JSON
        return response()->json([
            'message' => 'Data successfully updated.'
        ], 201);
    }

    public function destroy($uuid){
        // Find the record by uuid
        $outsideEc = AllAffected::where('uuid', $uuid)->first();

        // Delete the record
        $outsideEc->delete();

        // Return a success response
        return response()->json(['message' => 'Record deleted successfully']);
    }

    public function batchSave(Request $request)
    {

        $records = $request->all();

        // Iterate over each record to prepare for insertion
        foreach ($records as &$record) {
            // Automatically generate UUID if not provided
            $record['uuid'] = Str::uuid();

            AllAffected::create([
                'uuid' => $record['uuid'],
                'province_psgc_code' => $record['province_psgc_code'],
                'municipality_psgc_code' => $record['municipality_psgc_code'],
                'brgy_psgc_code' => $record['brgy_psgc_code'],
                'affected_families' => $record['affected_families'],
                'affected_persons' => $record['affected_persons'],
                'cost_asst_brgy' => $record['cost_asst_brgy'],
                'disaster_report_uuid' => $record['disaster_report_uuid'],
                'partially_damaged' => $record['partially_damaged'],
                'totally_damaged' => $record['totally_damaged']
            ]);

        }

        // Return a success response
        return response()->json([
            'message' => 'Data successfully saved.',
            'data' => $records,
        ], 201);
    }

    public function getAffectedProvince($disaster_report_id){

        $sql_province = "SELECT
                t1.province_psgc_code,
                t1.NAME,
                t1.affected_families,
                t1.affected_persons,
                t1.totally_damaged,
                t1.partially_damaged,
                ( SELECT SUM ( fnfi_quantity * fnfi_cost ) AS total_amount FROM tbl_fnfi_assistance WHERE disaster_report_uuid = ? AND province_psgc_code = t1.province_psgc_code ) dswd_assistance,
                t1.cost_asst_brgy,
                t2.lgu_assistance,
                t2.ngo_assistance,
                t2.other_go_assistance
                FROM
                (
                SELECT
                    t1.province_psgc_code,
                    t2.NAME,
                    SUM ( t1.affected_families ) AS affected_families,
                    SUM ( t1.affected_persons ) AS affected_persons,
                    SUM ( t1.totally_damaged ) AS totally_damaged,
                    SUM ( t1.partially_damaged ) AS partially_damaged,
                    SUM ( t1.cost_asst_brgy ) AS cost_asst_brgy
                FROM
                    tbl_all_affected AS t1
                    LEFT JOIN lib_provinces AS t2 ON t1.province_psgc_code = t2.psgc_code
                WHERE
                    t1.disaster_report_uuid = ?
                GROUP BY
                    t1.province_psgc_code,
                    t2.NAME
                ) AS t1
                LEFT JOIN (
                SELECT
                    province_psgc_code,
                    SUM ( lgu_assistance ) AS lgu_assistance,
                    SUM ( ngo_assistance ) AS ngo_assistance,
                    SUM ( other_go_assistance ) AS other_go_assistance
                FROM
                    tbl_assistance_cost
                WHERE
                    disaster_report_uuid = ?
                GROUP BY
                    province_psgc_code
                ) AS t2 ON t1.province_psgc_code = t2.province_psgc_code
                ORDER BY
                t1.province_psgc_code;";

        $result['province'] = DB::select($sql_province, [$disaster_report_id, $disaster_report_id, $disaster_report_id]);

        $sql_municipality = "SELECT
                    t1.*,
                    (SELECT SUM(fnfi_quantity * fnfi_cost) AS total_amount
                    FROM tbl_fnfi_assistance
                    WHERE disaster_report_uuid = ?
                    AND municipality_psgc_code = t1.municipality_psgc_code) AS dswd_assistance,
                    (COALESCE(t1.cost_asst_brgy, 0) + COALESCE(t1.lgu_assistance, 0)) AS tot_lgu_assistance
                    FROM (
                    SELECT
                        t1.province_psgc_code,
                        t2.psgc_code AS municipality_psgc_code,
                        t2.name,
                        SUM(t1.affected_families) AS affected_families,
                        SUM(t1.affected_persons) AS affected_persons,
                        SUM(t1.totally_damaged) AS totally_damaged,
                        SUM(t1.partially_damaged) AS partially_damaged,
                        SUM(t1.cost_asst_brgy) AS cost_asst_brgy,
                        COALESCE(t3.lgu_assistance, 0) AS lgu_assistance,
                        COALESCE(t3.ngo_assistance, 0) AS ngo_assistance,
                        COALESCE(t3.other_go_assistance, 0) AS other_go_assistance
                    FROM tbl_all_affected AS t1
                    LEFT JOIN lib_municipalities AS t2 ON t1.municipality_psgc_code = t2.psgc_code
                    LEFT JOIN tbl_assistance_cost AS t3
                        ON t1.province_psgc_code = t3.province_psgc_code
                        AND t1.municipality_psgc_code = t3.municipality_psgc_code
                    WHERE t1.disaster_report_uuid = ?
                    GROUP BY
                        t1.province_psgc_code,
                        t2.psgc_code,
                        t2.name,
                        t3.lgu_assistance,
                        t3.ngo_assistance,
                        t3.other_go_assistance
                    ) AS t1";

        $result['municipality'] = DB::select($sql_municipality, [$disaster_report_id, $disaster_report_id]);

        $result['barangay'] = DB::table('tbl_all_affected as t1')
                    ->select(
                        't1.uuid',
                        't1.province_psgc_code',
                        't1.municipality_psgc_code',
                        't1.brgy_psgc_code',
                        't2.name',
                        't1.affected_families',
                        't1.affected_persons',
                        't1.totally_damaged',
                        't1.partially_damaged',
                        't1.cost_asst_brgy',
                        DB::raw('NULL as lgu_assistance'),
                        DB::raw('NULL as ngo_assistance'),
                        DB::raw('NULL as other_go_assistance')
                    )
                    ->leftJoin('lib_barangays as t2', 't1.brgy_psgc_code', '=', 't2.psgc_code')
                    ->where('t1.disaster_report_uuid', $disaster_report_id)
                    ->get();

        $result['region'] = DB::table(DB::raw('(
                        SELECT
                            t1.disaster_report_uuid,
                            SUM(t1.affected_families) AS affected_families,
                            SUM(t1.affected_persons) AS affected_persons,
                            SUM(t1.totally_damaged) AS totally_damaged,
                            SUM(t1.partially_damaged) AS partially_damaged,
                            SUM(t1.cost_asst_brgy) AS cost_asst_brgy
                        FROM tbl_all_affected AS t1
                        WHERE t1.disaster_report_uuid = ?
                        GROUP BY t1.disaster_report_uuid
                    ) AS t1'))
                    ->select(
                        DB::raw('0 AS psgc'),
                        DB::raw("'Region' AS region_name"),
                        't1.*',
                        DB::raw('(SELECT SUM(t1.fnfi_quantity * t1.fnfi_cost) AS total_amount
                                FROM tbl_fnfi_assistance AS t1
                                LEFT JOIN tbl_fnfis AS t2 ON t1.fnfi_uuid = t2.uuid
                                LEFT JOIN lib_provinces AS t3 ON t1.province_psgc_code = t3.psgc_code
                                WHERE t1.disaster_report_uuid = ?) AS dswd_assistance'),
                        't2.lgu_assistance',
                        't2.ngo_assistance',
                        't2.other_go_assistance'
                    )
                    ->leftJoin(DB::raw('(
                        SELECT
                            disaster_report_uuid,
                            SUM(lgu_assistance) AS lgu_assistance,
                            SUM(ngo_assistance) AS ngo_assistance,
                            SUM(other_go_assistance) AS other_go_assistance
                        FROM tbl_assistance_cost
                        WHERE disaster_report_uuid = ?
                        GROUP BY disaster_report_uuid
                    ) AS t2'), 't1.disaster_report_uuid', '=', 't2.disaster_report_uuid')
                    ->setBindings([$disaster_report_id, $disaster_report_id, $disaster_report_id]) // Pass dynamic values for the disaster report UUID
                    ->first();


        // Return a success response
        return response()->json($result, 201);

    }
    public function getMainSummaryReport($disaster_report_uuid){

        $sqlProvince = "SELECT
                    t1.*,
                    t3.name as province_name,
                    t2.dswd_assistance,
                    t2.cost_asst_brgy,
                    t2.lgu_assistance,
                    t2.ngo_assistance,
                    t2.other_go_assistance,
                    t2.tot_lgu_assistance
                    FROM
                    (
                    SELECT
                        t1.*,
                        t2.ec_cum,
                        t2.ec_now,
                        t2.inside_families_cum,
                        t2.inside_families_now,
                        t2.inside_persons_cum,
                        t2.inside_persons_now,
                        t3.outside_families_cum,
                        t3.outside_families_now,
                        t3.outside_persons_cum,
                        t3.outside_persons_now,
                        COALESCE(t2.inside_families_cum, 0) + COALESCE(t3.outside_families_cum, 0) AS total_families_cum,
                        COALESCE(t2.inside_families_now, 0) + COALESCE(t3.outside_families_now, 0) AS total_families_now,
                        COALESCE(t2.inside_persons_cum, 0) + COALESCE(t3.outside_persons_cum, 0) AS total_persons_cum,
                        COALESCE(t2.inside_persons_now, 0) + COALESCE(t3.outside_persons_now, 0) AS total_persons_now
                    FROM
                        (
                        SELECT
                        t1.province_psgc_code,
                        t1.municipality_psgc_code,
                        t2.name,
                        COUNT(t1.brgy_psgc_code) AS affected_brgy,
                        SUM(t1.affected_families) AS affected_families,
                        SUM(t1.affected_persons) AS affected_persons,
                        SUM(t1.totally_damaged) AS totally_damaged,
                        SUM(t1.partially_damaged) AS partially_damaged
                        FROM
                        tbl_all_affected t1
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
                        ) t1
                        LEFT JOIN (
                        SELECT
                        t1.municipality_psgc_code,
                        SUM(t1.ec_cum) AS ec_cum,
                        SUM(t1.ec_now) AS ec_now,
                        SUM(t1.families_cum) AS inside_families_cum,
                        SUM(t1.families_now) AS inside_families_now,
                        SUM(t1.persons_cum) AS inside_persons_cum,
                        SUM(t1.persons_now) AS inside_persons_now
                        FROM
                        tbl_inside_ec t1
                        WHERE
                        t1.disaster_report_uuid = ?
                        GROUP BY
                        t1.province_psgc_code,
                        t1.municipality_psgc_code
                        ) t2 ON t1.municipality_psgc_code = t2.municipality_psgc_code
                        LEFT JOIN (
                        SELECT
                        t1.host_municipality_psgc_code,
                        SUM(t1.aff_families_cum) AS outside_families_cum,
                        SUM(t1.aff_families_now) AS outside_families_now,
                        SUM(t1.aff_persons_cum) AS outside_persons_cum,
                        SUM(t1.aff_persons_now) AS outside_persons_now
                        FROM
                        tbl_outside_ec t1
                        WHERE
                        t1.disaster_report_uuid = ?
                        GROUP BY
                        t1.host_province_psgc_code,
                        t1.host_municipality_psgc_code
                        ) t3 ON t1.municipality_psgc_code = t3.host_municipality_psgc_code
                    ) t1
                    LEFT JOIN (
                    SELECT
                        t1.*,
                        (SELECT SUM(fnfi_quantity * fnfi_cost) AS total_amount FROM tbl_fnfi_assistance WHERE disaster_report_uuid = ? AND municipality_psgc_code = t1.municipality_psgc_code) AS dswd_assistance,
                        (COALESCE(t1.cost_asst_brgy, 0) + COALESCE(t1.lgu_assistance, 0)) AS tot_lgu_assistance
                    FROM
                        (
                        SELECT
                        t2.psgc_code AS municipality_psgc_code,
                        SUM(t1.cost_asst_brgy) AS cost_asst_brgy,
                        COALESCE(t3.lgu_assistance, 0) AS lgu_assistance,
                        COALESCE(t3.ngo_assistance, 0) AS ngo_assistance,
                        COALESCE(t3.other_go_assistance, 0) AS other_go_assistance
                        FROM
                        tbl_all_affected t1
                        LEFT JOIN lib_municipalities t2 ON t1.municipality_psgc_code = t2.psgc_code
                        LEFT JOIN tbl_assistance_cost t3 ON t1.province_psgc_code = t3.province_psgc_code AND t1.municipality_psgc_code = t3.municipality_psgc_code
                        WHERE
                        t1.disaster_report_uuid = ?
                        GROUP BY
                        t1.province_psgc_code,
                        t2.psgc_code,
                        t2.name,
                        t3.lgu_assistance,
                        t3.ngo_assistance,
                        t3.other_go_assistance
                        ) t1
                    ) t2 ON t1.municipality_psgc_code = t2.municipality_psgc_code
                    LEFT JOIN lib_provinces t3 ON t1.province_psgc_code = t3.psgc_code
        ";

            $result['summary'] = DB::select($sqlProvince, [
                $disaster_report_uuid,
                $disaster_report_uuid,
                $disaster_report_uuid,
                $disaster_report_uuid,
                $disaster_report_uuid
            ]);

        // Return a success response
        return response()->json($result, 201);

    }
}
