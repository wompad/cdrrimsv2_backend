<?php

namespace App\Http\Controllers;

use App\Models\SexAgeSectorData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class SexAgeSectorDataController extends Controller
{
    /**
     * Store new Sex Age Sector Data.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validation rules
        $validator = Validator::make($request->all(), [
            'province_psgc_code'        => 'required',
            'municipality_psgc_code'    => ['required',
                Rule::exists('tbl_inside_ec', 'municipality_psgc_code')
                ->where(function ($query) use ($request) {
                    $query->where('disaster_report_uuid', $request->disaster_report_uuid);
                }),
                Rule::unique('tbl_sex_age_sector_data', 'municipality_psgc_code')
                ->where(function ($query) use ($request) {
                    $query->where('disaster_report_uuid', $request->disaster_report_uuid);
                })
            ],
            'disaster_report_uuid'      => 'required|uuid',

            // Infant data
            'infant_male_cum'           => 'nullable|integer|min:1',
            'infant_male_now'           => 'nullable|integer|min:0|lte:infant_male_cum',
            'infant_female_cum'         => 'nullable|integer|min:1',
            'infant_female_now'         => 'nullable|integer|min:0|lte:infant_female_cum',

            // Toddler data
            'toddlers_male_cum'         => 'nullable|integer|min:1',
            'toddlers_male_now'         => 'nullable|integer|min:0|lte:toddlers_male_cum',
            'toddlers_female_cum'       => 'nullable|integer|min:1',
            'toddlers_female_now'       => 'nullable|integer|min:0|lte:toddlers_female_cum',

            // School-age data
            'preschoolers_male_cum'     => 'nullable|integer|min:1',
            'preschoolers_male_now'     => 'nullable|integer|min:0|lte:preschoolers_male_cum',
            'preschoolers_female_cum'   => 'nullable|integer|min:1',
            'preschoolers_female_now'   => 'nullable|integer|min:0|lte:preschoolers_female_cum',

            // School-age data
            'school_age_male_cum'       => 'nullable|integer|min:1',
            'school_age_male_now'       => 'nullable|integer|min:0|lte:school_age_male_cum',
            'school_age_female_cum'     => 'nullable|integer|min:1',
            'school_age_female_now'     => 'nullable|integer|min:0|lte:school_age_female_cum',

            // Teenage data
            'teenage_male_cum'          => 'nullable|integer|min:1',
            'teenage_male_now'          => 'nullable|integer|min:0|lte:teenage_male_cum',
            'teenage_female_cum'        => 'nullable|integer|min:1',
            'teenage_female_now'        => 'nullable|integer|min:0|lte:teenage_female_cum',

            // Adult data
            'adult_male_cum'            => 'nullable|integer|min:1',
            'adult_male_now'            => 'nullable|integer|min:0|lte:adult_male_cum',
            'adult_female_cum'          => 'nullable|integer|min:1',
            'adult_female_now'          => 'nullable|integer|min:0|lte:adult_female_cum',

            // Elderly data
            'elderly_male_cum'          => 'nullable|integer|min:1',
            'elderly_male_now'          => 'nullable|integer|min:0|lte:elderly_male_cum',
            'elderly_female_cum'        => 'nullable|integer|min:1',
            'elderly_female_now'        => 'nullable|integer|min:0|lte:elderly_female_cum',

            // Pregnant and Lactating
            'pregnant_cum'              => 'nullable|integer|min:1',
            'pregnant_now'              => 'nullable|integer|min:0|lte:pregnant_cum',

            'lactating_cum'             => 'nullable|integer|min:1',
            'lactating_now'             => 'nullable|integer|min:0|lte:lactating_cum',

            // Child-headed families
            'child_headed_male_cum'     => 'nullable|integer|min:1',
            'child_headed_male_now'     => 'nullable|integer|min:0|lte:child_headed_male_cum',
            'child_headed_female_cum'   => 'nullable|integer|min:1',
            'child_headed_female_now'   => 'nullable|integer|min:0|lte:child_headed_female_cum',

            // Single-headed households
            'single_headed_male_cum'    => 'nullable|integer|min:1',
            'single_headed_male_now'    => 'nullable|integer|min:0|lte:single_headed_male_cum',
            'single_headed_female_cum'  => 'nullable|integer|min:1',
            'single_headed_female_now'  => 'nullable|integer|min:0|lte:single_headed_female_cum',

            // Solo parent
            'solo_parent_male_cum'      => 'nullable|integer|min:1',
            'solo_parent_male_now'      => 'nullable|integer|min:0|lte:solo_parent_male_cum',
            'solo_parent_female_cum'    => 'nullable|integer|min:1',
            'solo_parent_female_now'    => 'nullable|integer|min:0|lte:solo_parent_female_cum',

            // PWD (People with Disabilities)
            'pwd_male_cum'              => 'nullable|integer|min:1',
            'pwd_male_now'              => 'nullable|integer|min:0|lte:pwd_male_cum',
            'pwd_female_cum'            => 'nullable|integer|min:1',
            'pwd_female_now'            => 'nullable|integer|min:0|lte:pwd_female_cum',

            // IP (Indigenous People)
            'ip_male_cum'               => 'nullable|integer|min:1',
            'ip_male_now'               => 'nullable|integer|min:0|lte:ip_male_cum',
            'ip_female_cum'             => 'nullable|integer|min:1',
            'ip_female_now'             => 'nullable|integer|min:0|lte:ip_female_cum',

            // 4Ps (Pantawid Pamilyang Pilipino Program)
            'fourps_male_cum'           => 'nullable|integer|min:1',
            'fourps_male_now'           => 'nullable|integer|min:0|lte:fourps_male_cum',
            'fourps_female_cum'         => 'nullable|integer|min:1',
            'fourps_female_now'         => 'nullable|integer|min:0|lte:fourps_female_cum',
        ],[
            'municipality_psgc_code.exists'      => 'There are no reports on IDPs in this city/municipality',
            'municipality_psgc_code.unique'      => 'Municipality already existed'
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            return response()->json([
                'message'=> 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Save the data if validation passes
        $data = SexAgeSectorData::create([
            'uuid'                          => \Str::uuid(),
            'province_psgc_code'            => $request->province_psgc_code,
            'municipality_psgc_code'        => $request->municipality_psgc_code,
            'disaster_report_uuid'          => $request->disaster_report_uuid,

            'infant_male_cum'               => $request->infant_male_cum ?: 0,
            'infant_male_now'               => $request->infant_male_now ?: 0,
            'infant_female_cum'             => $request->infant_female_cum ?: 0,
            'infant_female_now'             => $request->infant_female_now ?: 0,

            'toddlers_male_cum'             => $request->toddlers_male_cum ?: 0,
            'toddlers_male_now'             => $request->toddlers_male_now ?: 0,
            'toddlers_female_cum'           => $request->toddlers_female_cum ?: 0,
            'toddlers_female_now'           => $request->toddlers_female_now ?: 0,

            'preschoolers_male_cum'         => $request->preschoolers_male_cum ?: 0,
            'preschoolers_male_now'         => $request->preschoolers_male_now ?: 0,
            'preschoolers_female_cum'       => $request->preschoolers_female_cum ?: 0,
            'preschoolers_female_now'       => $request->preschoolers_female_now ?: 0,

            'school_age_male_cum'           => $request->school_age_male_cum ?: 0,
            'school_age_male_now'           => $request->school_age_male_now ?: 0,
            'school_age_female_cum'         => $request->school_age_female_cum ?: 0,
            'school_age_female_now'         => $request->school_age_female_now ?: 0,

            'teenage_male_cum'              => $request->teenage_male_cum ?: 0,
            'teenage_male_now'              => $request->teenage_male_now ?: 0,
            'teenage_female_cum'            => $request->teenage_female_cum ?: 0,
            'teenage_female_now'            => $request->teenage_female_now ?: 0,

            'adult_male_cum'                => $request->adult_male_cum ?: 0,
            'adult_male_now'                => $request->adult_male_now ?: 0,
            'adult_female_cum'              => $request->adult_female_cum ?: 0,
            'adult_female_now'              => $request->adult_female_now ?: 0,

            'elderly_male_cum'              => $request->elderly_male_cum ?: 0,
            'elderly_male_now'              => $request->elderly_male_now ?: 0,
            'elderly_female_cum'            => $request->elderly_female_cum ?: 0,
            'elderly_female_now'            => $request->elderly_female_now ?: 0,

            'pregnant_cum'                  => $request->pregnant_cum ?: 0,
            'pregnant_now'                  => $request->pregnant_now ?: 0,

            'lactating_cum'                 => $request->lactating_cum ?: 0,
            'lactating_now'                 => $request->lactating_now ?: 0,

            'child_headed_male_cum'         => $request->child_headed_male_cum ?: 0,
            'child_headed_male_now'         => $request->child_headed_male_now ?: 0,
            'child_headed_female_cum'       => $request->child_headed_female_cum ?: 0,
            'child_headed_female_now'       => $request->child_headed_female_now ?: 0,

            'single_headed_male_cum'        => $request->single_headed_male_cum ?: 0,
            'single_headed_male_now'        => $request->single_headed_male_now ?: 0,
            'single_headed_female_cum'      => $request->single_headed_female_cum ?: 0,
            'single_headed_female_now'      => $request->single_headed_female_now ?: 0,

            'solo_parent_male_cum'          => $request->solo_parent_male_cum ?: 0,
            'solo_parent_male_now'          => $request->solo_parent_male_now ?: 0,
            'solo_parent_female_cum'        => $request->solo_parent_female_cum ?: 0,
            'solo_parent_female_now'        => $request->solo_parent_female_now ?: 0,

            'pwd_male_cum'                  => $request->pwd_male_cum ?: 0,
            'pwd_male_now'                  => $request->pwd_male_now ?: 0,
            'pwd_female_cum'                => $request->pwd_female_cum ?: 0,
            'pwd_female_now'                => $request->pwd_female_now ?: 0,

            'ip_male_cum'                   => $request->ip_male_cum ?: 0,
            'ip_male_now'                   => $request->ip_male_now ?: 0,
            'ip_female_cum'                 => $request->ip_female_cum ?: 0,
            'ip_female_now'                 => $request->ip_female_now ?: 0,

            'fourps_male_cum'               => $request->fourps_male_cum ?: 0,
            'fourps_male_now'               => $request->fourps_male_now ?: 0,
            'fourps_female_cum'             => $request->fourps_female_cum ?: 0,
            'fourps_female_now'             => $request->fourps_female_now ?: 0,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Data saved successfully',
            'data' => $data
        ], 201);
    }
    public function update(Request $request, $uuid)
    {
        // Validation rules
        $validator = Validator::make($request->all(), [
            'province_psgc_code'        => 'required',
            'municipality_psgc_code'    => 'required',

            // Infant data
            'infant_male_cum'           => 'nullable|integer|min:1',
            'infant_male_now'           => 'nullable|integer|min:0|lte:infant_male_cum',
            'infant_female_cum'         => 'nullable|integer|min:1',
            'infant_female_now'         => 'nullable|integer|min:0|lte:infant_female_cum',

            // Toddler data
            'toddlers_male_cum'         => 'nullable|integer|min:1',
            'toddlers_male_now'         => 'nullable|integer|min:0|lte:toddlers_male_cum',
            'toddlers_female_cum'       => 'nullable|integer|min:1',
            'toddlers_female_now'       => 'nullable|integer|min:0|lte:toddlers_female_cum',

            // School-age data
            'preschoolers_male_cum'     => 'nullable|integer|min:1',
            'preschoolers_male_now'     => 'nullable|integer|min:0|lte:preschoolers_male_cum',
            'preschoolers_female_cum'   => 'nullable|integer|min:1',
            'preschoolers_female_now'   => 'nullable|integer|min:0|lte:preschoolers_female_cum',

            // School-age data
            'school_age_male_cum'       => 'nullable|integer|min:1',
            'school_age_male_now'       => 'nullable|integer|min:0|lte:school_age_male_cum',
            'school_age_female_cum'     => 'nullable|integer|min:1',
            'school_age_female_now'     => 'nullable|integer|min:0|lte:school_age_female_cum',

            // Teenage data
            'teenage_male_cum'          => 'nullable|integer|min:1',
            'teenage_male_now'          => 'nullable|integer|min:0|lte:teenage_male_cum',
            'teenage_female_cum'        => 'nullable|integer|min:1',
            'teenage_female_now'        => 'nullable|integer|min:0|lte:teenage_female_cum',

            // Adult data
            'adult_male_cum'            => 'nullable|integer|min:1',
            'adult_male_now'            => 'nullable|integer|min:0|lte:adult_male_cum',
            'adult_female_cum'          => 'nullable|integer|min:1',
            'adult_female_now'          => 'nullable|integer|min:0|lte:adult_female_cum',

            // Elderly data
            'elderly_male_cum'          => 'nullable|integer|min:1',
            'elderly_male_now'          => 'nullable|integer|min:0|lte:elderly_male_cum',
            'elderly_female_cum'        => 'nullable|integer|min:1',
            'elderly_female_now'        => 'nullable|integer|min:0|lte:elderly_female_cum',

            // Pregnant and Lactating
            'pregnant_cum'              => 'nullable|integer|min:1',
            'pregnant_now'              => 'nullable|integer|min:0|lte:pregnant_cum',

            'lactating_cum'             => 'nullable|integer|min:1',
            'lactating_now'             => 'nullable|integer|min:0|lte:lactating_cum',

            // Child-headed families
            'child_headed_male_cum'     => 'nullable|integer|min:1',
            'child_headed_male_now'     => 'nullable|integer|min:0|lte:child_headed_male_cum',
            'child_headed_female_cum'   => 'nullable|integer|min:1',
            'child_headed_female_now'   => 'nullable|integer|min:0|lte:child_headed_female_cum',

            // Single-headed households
            'single_headed_male_cum'    => 'nullable|integer|min:1',
            'single_headed_male_now'    => 'nullable|integer|min:0|lte:single_headed_male_cum',
            'single_headed_female_cum'  => 'nullable|integer|min:1',
            'single_headed_female_now'  => 'nullable|integer|min:0|lte:single_headed_female_cum',

            // Solo parent
            'solo_parent_male_cum'      => 'nullable|integer|min:1',
            'solo_parent_male_now'      => 'nullable|integer|min:0|lte:solo_parent_male_cum',
            'solo_parent_female_cum'    => 'nullable|integer|min:1',
            'solo_parent_female_now'    => 'nullable|integer|min:0|lte:solo_parent_female_cum',

            // PWD (People with Disabilities)
            'pwd_male_cum'              => 'nullable|integer|min:1',
            'pwd_male_now'              => 'nullable|integer|min:0|lte:pwd_male_cum',
            'pwd_female_cum'            => 'nullable|integer|min:1',
            'pwd_female_now'            => 'nullable|integer|min:0|lte:pwd_female_cum',

            // IP (Indigenous People)
            'ip_male_cum'               => 'nullable|integer|min:1',
            'ip_male_now'               => 'nullable|integer|min:0|lte:ip_male_cum',
            'ip_female_cum'             => 'nullable|integer|min:1',
            'ip_female_now'             => 'nullable|integer|min:0|lte:ip_female_cum',

            // 4Ps (Pantawid Pamilyang Pilipino Program)
            'fourps_male_cum'           => 'nullable|integer|min:1',
            'fourps_male_now'           => 'nullable|integer|min:0|lte:fourps_male_cum',
            'fourps_female_cum'         => 'nullable|integer|min:1',
            'fourps_female_now'         => 'nullable|integer|min:0|lte:fourps_female_cum',
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            return response()->json([
                'message'=> 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        $sadd = SexAgeSectorData::findOrFail($uuid);

        // Save the data if validation passes
        $sadd->update([

            'infant_male_cum'               => $request->infant_male_cum ?: 0,
            'infant_male_now'               => $request->infant_male_now ?: 0,
            'infant_female_cum'             => $request->infant_female_cum ?: 0,
            'infant_female_now'             => $request->infant_female_now ?: 0,

            'toddlers_male_cum'             => $request->toddlers_male_cum ?: 0,
            'toddlers_male_now'             => $request->toddlers_male_now ?: 0,
            'toddlers_female_cum'           => $request->toddlers_female_cum ?: 0,
            'toddlers_female_now'           => $request->toddlers_female_now ?: 0,

            'preschoolers_male_cum'         => $request->preschoolers_male_cum ?: 0,
            'preschoolers_male_now'         => $request->preschoolers_male_now ?: 0,
            'preschoolers_female_cum'       => $request->preschoolers_female_cum ?: 0,
            'preschoolers_female_now'       => $request->preschoolers_female_now ?: 0,

            'school_age_male_cum'           => $request->school_age_male_cum ?: 0,
            'school_age_male_now'           => $request->school_age_male_now ?: 0,
            'school_age_female_cum'         => $request->school_age_female_cum ?: 0,
            'school_age_female_now'         => $request->school_age_female_now ?: 0,

            'teenage_male_cum'              => $request->teenage_male_cum ?: 0,
            'teenage_male_now'              => $request->teenage_male_now ?: 0,
            'teenage_female_cum'            => $request->teenage_female_cum ?: 0,
            'teenage_female_now'            => $request->teenage_female_now ?: 0,

            'adult_male_cum'                => $request->adult_male_cum ?: 0,
            'adult_male_now'                => $request->adult_male_now ?: 0,
            'adult_female_cum'              => $request->adult_female_cum ?: 0,
            'adult_female_now'              => $request->adult_female_now ?: 0,

            'elderly_male_cum'              => $request->elderly_male_cum ?: 0,
            'elderly_male_now'              => $request->elderly_male_now ?: 0,
            'elderly_female_cum'            => $request->elderly_female_cum ?: 0,
            'elderly_female_now'            => $request->elderly_female_now ?: 0,

            'pregnant_cum'                  => $request->pregnant_cum ?: 0,
            'pregnant_now'                  => $request->pregnant_now ?: 0,

            'lactating_cum'                 => $request->lactating_cum ?: 0,
            'lactating_now'                 => $request->lactating_now ?: 0,

            'child_headed_male_cum'         => $request->child_headed_male_cum ?: 0,
            'child_headed_male_now'         => $request->child_headed_male_now ?: 0,
            'child_headed_female_cum'       => $request->child_headed_female_cum ?: 0,
            'child_headed_female_now'       => $request->child_headed_female_now ?: 0,

            'single_headed_male_cum'        => $request->single_headed_male_cum ?: 0,
            'single_headed_male_now'        => $request->single_headed_male_now ?: 0,
            'single_headed_female_cum'      => $request->single_headed_female_cum ?: 0,
            'single_headed_female_now'      => $request->single_headed_female_now ?: 0,

            'solo_parent_male_cum'          => $request->solo_parent_male_cum ?: 0,
            'solo_parent_male_now'          => $request->solo_parent_male_now ?: 0,
            'solo_parent_female_cum'        => $request->solo_parent_female_cum ?: 0,
            'solo_parent_female_now'        => $request->solo_parent_female_now ?: 0,

            'pwd_male_cum'                  => $request->pwd_male_cum ?: 0,
            'pwd_male_now'                  => $request->pwd_male_now ?: 0,
            'pwd_female_cum'                => $request->pwd_female_cum ?: 0,
            'pwd_female_now'                => $request->pwd_female_now ?: 0,

            'ip_male_cum'                   => $request->ip_male_cum ?: 0,
            'ip_male_now'                   => $request->ip_male_now ?: 0,
            'ip_female_cum'                 => $request->ip_female_cum ?: 0,
            'ip_female_now'                 => $request->ip_female_now ?: 0,

            'fourps_male_cum'               => $request->fourps_male_cum ?: 0,
            'fourps_male_now'               => $request->fourps_male_now ?: 0,
            'fourps_female_cum'             => $request->fourps_female_cum ?: 0,
            'fourps_female_now'             => $request->fourps_female_now ?: 0,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Data updated successfully',
        ], 201);
    }
    public function destroy($uuid)
    {
        $sadd = SexAgeSectorData::findOrFail($uuid);

        // Delete the evacuation center
        $sadd->delete();

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'Data deleted successfully!'
        ], 200);
    }
    public function getAllSADData($disaster_report_uuid)
    {

        $sqlRegion = "SELECT
                        SUM(t1.infant_male_cum) infant_male_cum,
                        SUM(t1.infant_male_now) infant_male_now,
                        SUM(t1.infant_female_cum) infant_female_cum,
                        SUM(t1.infant_female_now) infant_female_now,
                        SUM(t1.toddlers_male_cum) toddlers_male_cum,
                        SUM(t1.toddlers_male_now) toddlers_male_now,
                        SUM(t1.toddlers_female_cum) toddlers_female_cum,
                        SUM(t1.toddlers_female_now) toddlers_female_now,
                        SUM(t1.preschoolers_male_cum) preschoolers_male_cum,
                        SUM(t1.preschoolers_male_now) preschoolers_male_now,
                        SUM(t1.preschoolers_female_cum) preschoolers_female_cum,
                        SUM(t1.preschoolers_female_now) preschoolers_female_now,
                        SUM(t1.school_age_male_cum) school_age_male_cum,
                        SUM(t1.school_age_male_now) school_age_male_now,
                        SUM(t1.school_age_female_cum) school_age_female_cum,
                        SUM(t1.school_age_female_now) school_age_female_now,
                        SUM(t1.teenage_male_cum) teenage_male_cum,
                        SUM(t1.teenage_male_now) teenage_male_now,
                        SUM(t1.teenage_female_cum) teenage_female_cum,
                        SUM(t1.teenage_female_now) teenage_female_now,
                        SUM(t1.adult_male_cum) adult_male_cum,
                        SUM(t1.adult_male_now) adult_male_now,
                        SUM(t1.adult_female_cum) adult_female_cum,
                        SUM(t1.adult_female_now) adult_female_now,
                        SUM(t1.elderly_male_cum) elderly_male_cum,
                        SUM(t1.elderly_male_now) elderly_male_now,
                        SUM(t1.elderly_female_cum) elderly_female_cum,
                        SUM(t1.elderly_female_now) elderly_female_now,
                        SUM(t1.pregnant_cum) pregnant_cum,
                        SUM(t1.pregnant_now) pregnant_now,
                        SUM(t1.lactating_cum) lactating_cum,
                        SUM(t1.lactating_now) lactating_now,
                        SUM(t1.child_headed_male_cum) child_headed_male_cum,
                        SUM(t1.child_headed_male_now) child_headed_male_now,
                        SUM(t1.child_headed_female_cum) child_headed_female_cum,
                        SUM(t1.child_headed_female_now) child_headed_female_now,
                        SUM(t1.single_headed_male_cum) single_headed_male_cum,
                        SUM(t1.single_headed_male_now) single_headed_male_now,
                        SUM(t1.single_headed_female_cum) single_headed_female_cum,
                        SUM(t1.single_headed_female_now) single_headed_female_now,
                        SUM(t1.solo_parent_male_cum) solo_parent_male_cum,
                        SUM(t1.solo_parent_male_now) solo_parent_male_now,
                        SUM(t1.solo_parent_female_cum) solo_parent_female_cum,
                        SUM(t1.solo_parent_female_now) solo_parent_female_now,
                        SUM(t1.pwd_male_cum) pwd_male_cum,
                        SUM(t1.pwd_male_now) pwd_male_now,
                        SUM(t1.pwd_female_cum) pwd_female_cum,
                        SUM(t1.pwd_female_now) pwd_female_now,
                        SUM(t1.ip_male_cum) ip_male_cum,
                        SUM(t1.ip_male_now) ip_male_now,
                        SUM(t1.ip_female_cum) ip_female_cum,
                        SUM(t1.ip_female_now) ip_female_now,
                        SUM(t1.fourps_male_cum) fourps_male_cum,
                        SUM(t1.fourps_male_now) fourps_male_now,
                        SUM(t1.fourps_female_cum) fourps_female_cum,
                        SUM(t1.fourps_female_now) fourps_female_now
                        FROM
                        tbl_sex_age_sector_data t1
                        WHERE t1.disaster_report_uuid = ?
                    ";

        $result['region'] = DB::select($sqlRegion, [$disaster_report_uuid]);

        $sqlProvince = "SELECT
                        t1.province_psgc_code,
                        t2.name,
                        SUM(t1.infant_male_cum) infant_male_cum,
                        SUM(t1.infant_male_now) infant_male_now,
                        SUM(t1.infant_female_cum) infant_female_cum,
                        SUM(t1.infant_female_now) infant_female_now,
                        SUM(t1.toddlers_male_cum) toddlers_male_cum,
                        SUM(t1.toddlers_male_now) toddlers_male_now,
                        SUM(t1.toddlers_female_cum) toddlers_female_cum,
                        SUM(t1.toddlers_female_now) toddlers_female_now,
                        SUM(t1.preschoolers_male_cum) preschoolers_male_cum,
                        SUM(t1.preschoolers_male_now) preschoolers_male_now,
                        SUM(t1.preschoolers_female_cum) preschoolers_female_cum,
                        SUM(t1.preschoolers_female_now) preschoolers_female_now,
                        SUM(t1.school_age_male_cum) school_age_male_cum,
                        SUM(t1.school_age_male_now) school_age_male_now,
                        SUM(t1.school_age_female_cum) school_age_female_cum,
                        SUM(t1.school_age_female_now) school_age_female_now,
                        SUM(t1.teenage_male_cum) teenage_male_cum,
                        SUM(t1.teenage_male_now) teenage_male_now,
                        SUM(t1.teenage_female_cum) teenage_female_cum,
                        SUM(t1.teenage_female_now) teenage_female_now,
                        SUM(t1.adult_male_cum) adult_male_cum,
                        SUM(t1.adult_male_now) adult_male_now,
                        SUM(t1.adult_female_cum) adult_female_cum,
                        SUM(t1.adult_female_now) adult_female_now,
                        SUM(t1.elderly_male_cum) elderly_male_cum,
                        SUM(t1.elderly_male_now) elderly_male_now,
                        SUM(t1.elderly_female_cum) elderly_female_cum,
                        SUM(t1.elderly_female_now) elderly_female_now,
                        SUM(t1.pregnant_cum) pregnant_cum,
                        SUM(t1.pregnant_now) pregnant_now,
                        SUM(t1.lactating_cum) lactating_cum,
                        SUM(t1.lactating_now) lactating_now,
                        SUM(t1.child_headed_male_cum) child_headed_male_cum,
                        SUM(t1.child_headed_male_now) child_headed_male_now,
                        SUM(t1.child_headed_female_cum) child_headed_female_cum,
                        SUM(t1.child_headed_female_now) child_headed_female_now,
                        SUM(t1.single_headed_male_cum) single_headed_male_cum,
                        SUM(t1.single_headed_male_now) single_headed_male_now,
                        SUM(t1.single_headed_female_cum) single_headed_female_cum,
                        SUM(t1.single_headed_female_now) single_headed_female_now,
                        SUM(t1.solo_parent_male_cum) solo_parent_male_cum,
                        SUM(t1.solo_parent_male_now) solo_parent_male_now,
                        SUM(t1.solo_parent_female_cum) solo_parent_female_cum,
                        SUM(t1.solo_parent_female_now) solo_parent_female_now,
                        SUM(t1.pwd_male_cum) pwd_male_cum,
                        SUM(t1.pwd_male_now) pwd_male_now,
                        SUM(t1.pwd_female_cum) pwd_female_cum,
                        SUM(t1.pwd_female_now) pwd_female_now,
                        SUM(t1.ip_male_cum) ip_male_cum,
                        SUM(t1.ip_male_now) ip_male_now,
                        SUM(t1.ip_female_cum) ip_female_cum,
                        SUM(t1.ip_female_now) ip_female_now,
                        SUM(t1.fourps_male_cum) fourps_male_cum,
                        SUM(t1.fourps_male_now) fourps_male_now,
                        SUM(t1.fourps_female_cum) fourps_female_cum,
                        SUM(t1.fourps_female_now) fourps_female_now
                        FROM
                        tbl_sex_age_sector_data t1
                        LEFT JOIN lib_provinces t2 ON t1.province_psgc_code = t2.psgc_code
                        WHERE t1.disaster_report_uuid = ?
                        GROUP BY t1.province_psgc_code, t2.name
                        ORDER BY
                        t1.province_psgc_code
                    ";

        $result['province'] = DB::select($sqlProvince, [$disaster_report_uuid]);

        $sqlMunicipality = "SELECT
                            t2.name,
                            t1.*
                            FROM
                            tbl_sex_age_sector_data t1
                            LEFT JOIN lib_municipalities t2 ON t1.municipality_psgc_code = t2.psgc_code
                            WHERE t1.disaster_report_uuid = ?
                            ORDER BY
                            t1.province_psgc_code,
                            t1.municipality_psgc_code
                    ";

        $result['municipality'] = DB::select($sqlMunicipality, [$disaster_report_uuid]);

        return response()->json($result, 201);

    }
}
