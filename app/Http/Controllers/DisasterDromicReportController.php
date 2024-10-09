<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DisasterDromicReport;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Validator;

class DisasterDromicReportController extends Controller
{
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'incident_id'               => 'required|exists:tbl_dromic,uuid',
            'report_name'               => 'required|string|max:255',
            'report_date'               => 'required|date',
            'as_of_time'                => 'required|date_format:H:i',
            'prepared_by'               => 'required|string|max:255',
            'recommended_by'            => 'required|string|max:255',
            'approved_by'               => 'required|string|max:255',
            'prepared_by_position'      => 'required|string|max:255',
            'recommended_by_position'   => 'required|string|max:255',
            'approved_by_position'      => 'required|string|max:255',
            'created_by'                => 'required|exists:auth_users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Create a new disaster report
        $disasterReport = DisasterDromicReport::create([
            'uuid'                      => Str::uuid(),
            'incident_id'               => $request->incident_id,
            'report_name'               => $request->report_name,
            'report_date'               => $request->report_date,
            'as_of_time'                => $request->as_of_time,
            'prepared_by'               => strtoupper($request->prepared_by),
            'recommended_by'            => strtoupper($request->recommended_by),
            'approved_by'               => strtoupper($request->approved_by),
            'prepared_by_position'      => $request->prepared_by_position,
            'recommended_by_position'   => $request->recommended_by_position,
            'approved_by_position'      => $request->approved_by_position,
            'created_by'                => $request->created_by
        ]);

        // Return a success response
        return response()->json([
            'message' => 'Disaster report created successfully.',
            'data' => $disasterReport
        ], 201);
    }
    public function newDROMICReport(Request $request){
        $existing_uuid = $request->input('existing_uuid');
        $new_uuid = $request->input('new_uuid');

        $existing_data_all_affected = DB::table('tbl_all_affected')
        ->where('disaster_report_uuid', $existing_uuid)
        ->get();

        foreach ($existing_data_all_affected as $data) {
            DB::table('tbl_all_affected')->insert([
                'uuid'                      => \Str::uuid(), // Generate a new UUID for each row
                'province_psgc_code'        => $data->province_psgc_code,
                'municipality_psgc_code'    => $data->municipality_psgc_code,
                'brgy_psgc_code'            => $data->brgy_psgc_code,
                'totally_damaged'           => $data->totally_damaged,
                'partially_damaged'         => $data->partially_damaged,
                'disaster_report_uuid'      => $new_uuid,
                'cost_asst_brgy'            => $data->cost_asst_brgy,
                'affected_families'         => $data->affected_families,
                'affected_persons'          => $data->affected_persons,
                'created_at'                => $data->created_at,
                'updated_at'                => now()
            ]);
        }

        $existing_data_inside_ec = DB::table('tbl_inside_ec')
        ->where('disaster_report_uuid', $existing_uuid)
        ->orderBy('province_psgc_code', 'asc')
        ->orderBy('municipality_psgc_code', 'asc')
        ->orderBy('brgy_located_ec_psgc_code', 'asc')
        ->orderBy('ec_uuid', 'asc')
        ->orderBy('created_at', 'asc')
        ->get();

        foreach ($existing_data_inside_ec as $data) {
            DB::table('tbl_inside_ec')->insert([
                'uuid'                                  => \Str::uuid(),
                'province_psgc_code'                    => $data->province_psgc_code,
                'municipality_psgc_code'                => $data->municipality_psgc_code,
                'municipality_located_ec_psgc_code'     => $data->municipality_located_ec_psgc_code,
                'brgy_located_ec_psgc_code'             => $data->brgy_located_ec_psgc_code,
                'ec_uuid'                               => $data->ec_uuid,
                'ec_cum'                                => $data->ec_cum,
                'ec_now'                                => $data->ec_now,
                'families_cum'                          => $data->families_cum,
                'families_now'                          => $data->families_now,
                'persons_cum'                           => $data->persons_cum,
                'persons_now'                           => $data->persons_now,
                'brgy_origin_psgc_codes'                => $data->brgy_origin_psgc_codes,
                'ec_status'                             => $data->ec_status,
                'ec_remarks'                            => $data->ec_remarks,
                'disaster_report_uuid'                  => $new_uuid,
                'created_at'                            => $data->created_at,
                'updated_at'                            => now()
            ]);
        }

        $existing_data_outside_ec = DB::table('tbl_outside_ec')
        ->where('disaster_report_uuid', $existing_uuid)
        ->get();

        foreach ($existing_data_outside_ec as $data) {
            DB::table('tbl_outside_ec')->insert([
                'uuid'                          => \Str::uuid(),
                'disaster_report_uuid'          => $new_uuid,
                'host_province_psgc_code'       => $data->host_province_psgc_code,
                'host_municipality_psgc_code'   => $data->host_municipality_psgc_code,
                'host_brgy_psgc_code'           => $data->host_brgy_psgc_code,
                'aff_families_cum'              => $data->aff_families_cum,
                'aff_families_now'              => $data->aff_families_now,
                'aff_persons_cum'               => $data->aff_persons_cum,
                'aff_persons_now'               => $data->aff_persons_now,
                'origin_province_psgc_code'     => $data->origin_province_psgc_code,
                'origin_municipality_psgc_code' => $data->origin_municipality_psgc_code,
                'origin_brgy_psgc_code'         => $data->origin_brgy_psgc_code,
                'created_at'                    => $data->created_at,
                'updated_at'                    => now()
            ]);
        }

        $existing_data_assistance_cost = DB::table('tbl_assistance_cost')
        ->where('disaster_report_uuid', $existing_uuid)
        ->get();

        foreach ($existing_data_assistance_cost as $data) {
            DB::table('tbl_assistance_cost')->insert([
                'uuid'                      => \Str::uuid(),
                'disaster_report_uuid'      => $new_uuid,
                'province_psgc_code'        => $data->province_psgc_code,
                'municipality_psgc_code'    => $data->municipality_psgc_code,
                'lgu_assistance'            => $data->lgu_assistance,
                'ngo_assistance'            => $data->ngo_assistance,
                'other_go_assistance'       => $data->other_go_assistance,
                'created_at'                => $data->created_at,
                'updated_at'                => now()
            ]);
        }

        $existing_data_fnfi_assistance = DB::table('tbl_fnfi_assistance')
        ->where('disaster_report_uuid', $existing_uuid)
        ->get();

        foreach ($existing_data_fnfi_assistance as $data) {
            DB::table('tbl_fnfi_assistance')->insert([
                'uuid'                      => \Str::uuid(),
                'disaster_report_uuid'      => $new_uuid,
                'fnfi_uuid'                 => $data->fnfi_uuid,
                'fnfi_cost'                 => $data->fnfi_cost,
                'fnfi_quantity'             => $data->fnfi_quantity,
                'augmentation_date'         => $data->augmentation_date,
                'province_psgc_code'        => $data->province_psgc_code,
                'municipality_psgc_code'    => $data->municipality_psgc_code,
                'created_at'                => $data->created_at,
                'updated_at'                => now()
            ]);
        }

        $existing_data_sadd = DB::table('tbl_sex_age_sector_data')
        ->where('disaster_report_uuid', $existing_uuid)
        ->get();

        foreach ($existing_data_sadd as $data) {
            DB::table('tbl_sex_age_sector_data')->insert([
                'uuid'                      => \Str::uuid(),
                'province_psgc_code'        => $data->province_psgc_code,
                'municipality_psgc_code'    => $data->municipality_psgc_code,
                'disaster_report_uuid'      => $new_uuid,

                'infant_male_cum'           => $data->infant_male_cum,
                'infant_male_now'           => $data->infant_male_now,
                'infant_female_cum'         => $data->infant_female_cum,
                'infant_female_now'         => $data->infant_female_now,
                'toddlers_male_cum'         => $data->toddlers_male_cum,
                'toddlers_male_now'         => $data->toddlers_male_now,
                'toddlers_female_cum'       => $data->toddlers_female_cum,
                'toddlers_female_now'       => $data->toddlers_female_now,
                'school_age_male_cum'       => $data->school_age_male_cum,
                'school_age_male_now'       => $data->school_age_male_now,
                'school_age_female_cum'     => $data->school_age_female_cum,
                'school_age_female_now'     => $data->school_age_female_now,
                'teenage_male_cum'          => $data->teenage_male_cum,
                'teenage_male_now'          => $data->teenage_male_now,
                'teenage_female_cum'        => $data->teenage_female_cum,
                'teenage_female_now'        => $data->teenage_female_now,
                'adult_male_cum'            => $data->adult_male_cum,
                'adult_male_now'            => $data->adult_male_now,
                'adult_female_cum'          => $data->adult_female_cum,
                'adult_female_now'          => $data->adult_female_now,
                'elderly_male_cum'          => $data->elderly_male_cum,
                'elderly_male_now'          => $data->elderly_male_now,
                'elderly_female_cum'        => $data->elderly_female_cum,
                'elderly_female_now'        => $data->elderly_female_now,
                'pregnant_cum'              => $data->pregnant_cum,
                'pregnant_now'              => $data->pregnant_now,
                'lactating_cum'             => $data->lactating_cum,
                'lactating_now'             => $data->lactating_now,
                'child_headed_male_cum'     => $data->child_headed_male_cum,
                'child_headed_male_now'     => $data->child_headed_male_now,
                'child_headed_female_cum'   => $data->child_headed_female_cum,
                'child_headed_female_now'   => $data->child_headed_female_now,
                'single_headed_male_cum'    => $data->single_headed_male_cum,
                'single_headed_male_now'    => $data->single_headed_male_now,
                'single_headed_female_cum'  => $data->single_headed_female_cum,
                'single_headed_female_now'  => $data->single_headed_female_now,
                'solo_parent_male_cum'      => $data->solo_parent_male_cum,
                'solo_parent_male_now'      => $data->solo_parent_male_now,
                'solo_parent_female_cum'    => $data->solo_parent_female_cum,
                'solo_parent_female_now'    => $data->solo_parent_female_now,
                'pwd_male_cum'              => $data->pwd_male_cum,
                'pwd_male_now'              => $data->pwd_male_now,
                'pwd_female_cum'            => $data->pwd_female_cum,
                'pwd_female_now'            => $data->pwd_female_now,
                'ip_male_cum'               => $data->ip_male_cum,
                'ip_male_now'               => $data->ip_male_now,
                'ip_female_cum'             => $data->ip_female_cum,
                'ip_female_now'             => $data->ip_female_now,
                'fourps_male_cum'           => $data->fourps_male_cum,
                'fourps_male_now'           => $data->fourps_male_now,
                'fourps_female_cum'         => $data->fourps_female_cum,
                'fourps_female_now'         => $data->fourps_female_now,
                'preschoolers_male_cum'     => $data->preschoolers_male_cum,
                'preschoolers_male_now'     => $data->preschoolers_male_now,
                'preschoolers_female_cum'   => $data->preschoolers_female_cum,
                'preschoolers_female_now'   => $data->preschoolers_female_now,

                'created_at'                => $data->created_at,
                'updated_at'                => now()
            ]);
        }

        // Step 4: Return a success response
        return response()->json([
            'message' => 'All data successfully saved to new disaster report.'
        ], 200);

    }
}
