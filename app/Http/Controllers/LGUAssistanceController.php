<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class LGUAssistanceController extends Controller
{
    // Function to store or update assistance data
    public function storeOrUpdate(Request $request)
    {
        // Validate the incoming request
        try {
            // Validate the incoming request
            $validatedData = $request->validate([
                'disaster_report_uuid' => 'required|string',
                'province_psgc_code' => 'required|string',
                'municipality_psgc_code' => 'required|string',
                'lgu_assistance' => 'nullable|numeric',
                'ngo_assistance' => 'nullable|numeric',
                'other_go_assistance' => 'nullable|numeric',
            ]);
        } catch (ValidationException $e) {
            // Return validation errors with a 422 status code
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        // Check if the record exists based on disaster_report_uuid and municipality_psgc_code
        $existingRecord = DB::table('tbl_assistance_cost')
            ->where('disaster_report_uuid', $validatedData['disaster_report_uuid'])
            ->where('municipality_psgc_code', $validatedData['municipality_psgc_code'])
            ->first();

        // If the record exists, update the data
        if ($existingRecord) {
            DB::table('tbl_assistance_cost')
                ->where('disaster_report_uuid', $validatedData['disaster_report_uuid'])
                ->where('municipality_psgc_code', $validatedData['municipality_psgc_code'])
                ->update([
                    'lgu_assistance'        => $validatedData['lgu_assistance'] ?? $existingRecord->lgu_assistance,
                    'ngo_assistance'        => $validatedData['ngo_assistance'] ?? $existingRecord->ngo_assistance,
                    'other_go_assistance'   => $validatedData['other_go_assistance'] ?? $existingRecord->other_go_assistance,
                    'updated_at'            => Carbon::now()
                ]);

            return response()->json(['message' => 'Record updated successfully'], 200);
        }
        // Otherwise, insert a new record
        else {
            DB::table('tbl_assistance_cost')->insert([
                'uuid'                    => Str::uuid(),
                'disaster_report_uuid'    => $validatedData['disaster_report_uuid'],
                'province_psgc_code'      => $validatedData['province_psgc_code'],
                'municipality_psgc_code'  => $validatedData['municipality_psgc_code'],
                'lgu_assistance'          => $validatedData['lgu_assistance'] ?? 0,
                'ngo_assistance'          => $validatedData['ngo_assistance'] ?? 0,
                'other_go_assistance'     => $validatedData['other_go_assistance'] ?? 0,
                'created_at'              => Carbon::now()
            ]);

            return response()->json(['message' => 'Record saved successfully'], 201);
        }
    }
}
