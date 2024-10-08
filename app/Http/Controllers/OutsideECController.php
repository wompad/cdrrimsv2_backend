<?php

namespace App\Http\Controllers;

use App\Models\OutsideEC;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class OutsideECController extends Controller
{
    public function validateOutsideECRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'disaster_report_uuid'          => 'required',
            'host_province_psgc_code'       => 'required',
            'host_municipality_psgc_code'   => ['required',
                Rule::exists('tbl_all_affected', 'municipality_psgc_code')
                ->where(function ($query) use ($request) {
                    $query->where('disaster_report_uuid', $request->disaster_report_uuid);
                })
            ],
            'host_brgy_psgc_code'           => 'required',
            'aff_families_cum'              => 'required|numeric|min:1',
            'aff_families_now'              => 'required|numeric|min:0',
            'aff_persons_cum'               => 'required|numeric|min:1',
            'aff_persons_now'               => 'required|numeric|min:0',
            'origin_province_psgc_code'     => 'required',
            'origin_municipality_psgc_code' => 'required',
            'origin_brgy_psgc_code'         => 'required',
        ],[
            'host_province_psgc_code.required'       => 'Host province field is required',
            'host_municipality_psgc_code.required'   => 'Host municipality field is required',
            'host_municipality_psgc_code.exists'     => 'There are no reports on affected in this city/municipality for the given disaster report.',
            'host_brgy_psgc_code.required'           => 'Host barangay field is required',
            'aff_families_cum.required'              => 'Affected families cum field is required',
            'aff_families_now.required'              => 'Affected families now field is required',
            'aff_persons_cum.required'               => 'Affected persons cum field is required',
            'aff_persons_now.required'               => 'Affected persons now field is required',
            'origin_province_psgc_code.required'     => 'Origin province field is required',
            'origin_municipality_psgc_code.required' => 'Origin municipality field is required',
            'origin_brgy_psgc_code.required'         => 'Origin barangay field is required'
        ]);

        // Custom validation logic
        $validator->after(function ($validator) use ($request) {
            // Check if aff_families_now exceeds aff_families_cum
            if ($request->aff_families_now > $request->aff_families_cum) {
                $validator->errors()->add('aff_families_now', 'The current affected families cannot be greater than the cumulative affected families.');
            }

            // Check if aff_persons_now exceeds aff_persons_cum
            if ($request->aff_persons_now > $request->aff_persons_cum) {
                $validator->errors()->add('aff_persons_now', 'The current affected persons cannot be greater than the cumulative affected persons.');
            }

            // Check if aff_persons_cum is lower than aff_families_cum
            if ($request->aff_persons_cum < $request->aff_families_cum) {
                $validator->errors()->add('aff_persons_cum', 'The cumulative affected persons cannot be lower than the cumulative affected families.');
            }

            // Check if aff_persons_now is lower than aff_families_now
            if ($request->aff_persons_now < $request->aff_families_now) {
                $validator->errors()->add('aff_persons_now', 'The current affected persons cannot be lower than the current affected families.');
            }

        });

        if ($validator->fails()) {
            return response()->json([
                'message'=> 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        // If validation passes, proceed with the logic here
        return response()->json([
            'message' => 'Validation passed.',
        ], 200);
    }

    public function batchSave(Request $request)
    {

        $records = $request->all();

        // Iterate over each record to prepare for insertion
        foreach ($records as &$record) {
            // Automatically generate UUID if not provided
            $record['uuid'] = Str::uuid();

            OutsideEC::create([
                'uuid' => $record['uuid'],
                'disaster_report_uuid' => $record['disaster_report_uuid'],

                'host_province_psgc_code' => $record['host_province_psgc_code'],
                'host_municipality_psgc_code' => $record['host_municipality_psgc_code'],
                'host_brgy_psgc_code' => $record['host_brgy_psgc_code'],

                'aff_families_cum' => $record['aff_families_cum'],
                'aff_families_now' => $record['aff_families_now'],
                'aff_persons_cum' => $record['aff_persons_cum'],
                'aff_persons_now' => $record['aff_persons_now'],

                'origin_province_psgc_code' => $record['origin_province_psgc_code'],
                'origin_municipality_psgc_code' => $record['origin_municipality_psgc_code'],
                'origin_brgy_psgc_code' => $record['origin_brgy_psgc_code'],
            ]);

        }

        // Return a success response
        return response()->json([
            'message' => 'Data successfully saved.',
            'data' => $records,
        ], 201);
    }

    public function getAllOutsideEC($disaster_report_id)
    {
        $results['barangays'] = DB::table('tbl_outside_ec as t1')
            ->select(
                't1.uuid',
                't1.disaster_report_uuid',
                't1.host_province_psgc_code',
                't1.host_municipality_psgc_code',
                't1.host_brgy_psgc_code',
                't2.name as host_brgy_name',
                't1.aff_families_cum',
                't1.aff_families_now',
                't1.aff_persons_cum',
                't1.aff_persons_now',
                't1.origin_province_psgc_code',
                't1.origin_municipality_psgc_code',
                't1.origin_brgy_psgc_code',
                DB::raw('(SELECT name FROM lib_barangays WHERE psgc_code = t1.origin_brgy_psgc_code) as origin_brgy_name')
            )
            ->leftJoin('lib_barangays as t2', 't1.host_brgy_psgc_code', '=', 't2.psgc_code')
            ->where('t1.disaster_report_uuid', $disaster_report_id)
            ->get();

        $results['municipalities'] = DB::table('tbl_outside_ec as t1')
            ->select(
                't1.host_municipality_psgc_code',
                't2.name',
                't1.host_province_psgc_code',
                DB::raw('SUM(t1.aff_families_cum) as aff_families_cum'),
                DB::raw('SUM(t1.aff_families_now) as aff_families_now'),
                DB::raw('SUM(t1.aff_persons_cum) as aff_persons_cum'),
                DB::raw('SUM(t1.aff_persons_now) as aff_persons_now')
            )
            ->leftJoin('lib_municipalities as t2', 't1.host_municipality_psgc_code', '=', 't2.psgc_code')
            ->where('t1.disaster_report_uuid', $disaster_report_id)
            ->groupBy('t1.host_municipality_psgc_code', 't2.name','t1.host_province_psgc_code')
            ->get();

        $results['provinces'] = DB::table('tbl_outside_ec as t1')
            ->select(
                't1.host_province_psgc_code',
                't2.name',
                DB::raw('SUM(t1.aff_families_cum) as aff_families_cum'),
                DB::raw('SUM(t1.aff_families_now) as aff_families_now'),
                DB::raw('SUM(t1.aff_persons_cum) as aff_persons_cum'),
                DB::raw('SUM(t1.aff_persons_now) as aff_persons_now')
            )
            ->leftJoin('lib_provinces as t2', 't1.host_province_psgc_code', '=', 't2.psgc_code')
            ->where('t1.disaster_report_uuid', $disaster_report_id)
            ->groupBy('t1.host_province_psgc_code', 't2.name')
            ->get();


        $results['region'] = DB::table('tbl_outside_ec as t1')
            ->select(
                DB::raw('SUM(t1.aff_families_cum) as aff_families_cum'),
                DB::raw('SUM(t1.aff_families_now) as aff_families_now'),
                DB::raw('SUM(t1.aff_persons_cum) as aff_persons_cum'),
                DB::raw('SUM(t1.aff_persons_now) as aff_persons_now')
            )
            ->where('t1.disaster_report_uuid', $disaster_report_id)
            ->first();

        return response()->json($results, 201);

    }
    public function updateOutsideEC(Request $request){
        // Initial validation
        $validator = Validator::make($request->all(),
        [
            'aff_families_cum'  => 'required|integer|min:1',
            'aff_families_now'  => 'required|integer|min:0',
            'aff_persons_cum'   => 'required|integer|min:1',
            'aff_persons_now'   => 'required|integer|min:0'
        ]);

        // Custom validation logic
        $validator->after(function ($validator) use ($request) {
            // Check if aff_families_now exceeds aff_families_cum
            if ($request->aff_families_now > $request->aff_families_cum) {
                $validator->errors()->add('aff_families_now', 'The current affected families cannot be greater than the cumulative affected families.');
            }

            // Check if aff_persons_now exceeds aff_persons_cum
            if ($request->aff_persons_now > $request->aff_persons_cum) {
                $validator->errors()->add('aff_persons_now', 'The current affected persons cannot be greater than the cumulative affected persons.');
            }

            // Check if aff_persons_cum is lower than aff_families_cum
            if ($request->aff_persons_cum < $request->aff_families_cum) {
                $validator->errors()->add('aff_persons_cum', 'The cumulative affected persons cannot be lower than the cumulative affected families.');
            }

            // Check if aff_persons_now is lower than aff_families_now
            if ($request->aff_persons_now < $request->aff_families_now) {
                $validator->errors()->add('aff_persons_now', 'The current affected persons cannot be lower than the current affected families.');
            }

        });

        // Handle validation failure
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::table('tbl_outside_ec')
            ->where('uuid', $request->uuid)
            ->update([
                'aff_families_cum'  => $request->aff_families_cum,
                'aff_families_now'  => $request->aff_families_now,
                'aff_persons_cum'   => $request->aff_persons_cum,
                'aff_persons_now'   => $request->aff_persons_now
            ]);

        // Return a response, e.g., redirect or JSON
        return response()->json([
            'message' => 'Data successfully updated.'
        ], 201);
    }

    public function destroy($uuid)
    {
        // Find the record by uuid
        $outsideEc = OutsideEC::where('uuid', $uuid)->first();

        // Delete the record
        $outsideEc->delete();

        // Return a success response
        return response()->json(['message' => 'Record deleted successfully']);
    }
}
