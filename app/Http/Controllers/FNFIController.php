<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fnfis;
use Illuminate\Support\Str;

class FNFIController extends Controller
{
    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'fnfi_name'  => 'required|string|max:255',
            'item_price' => 'required|numeric',
            'fnfi_type'  => 'required|string|max:255'
        ],[
            'fnfi_name.required'    => 'Item/FNFI name is required',
            'item_price.required'   => 'Item price is required',
            'fnfi_type.required'    => 'Item type is required'
        ]
        );

        // Create a new record in tbl_fnfis with UUID
        $fnfi = Fnfis::create([
            'uuid'        => Str::uuid(), // Generate UUID
            'fnfi_name'   => $request->fnfi_name,
            'description' => $request->description,
            'item_price'  => $request->item_price,
            'fnfi_type'   => $request->fnfi_type
        ]);

        // Return a response
        return response()->json(['message' => 'Item successfully saved', 'data' => $fnfi], 201);
    }
    public function getAllItems()
    {
        // Fetch all records from tbl_fnfis
        $fnfis = Fnfis::orderByRaw("
                            fnfi_name = 'Family Food Pack' DESC,
                            LOWER(fnfi_name) LIKE '%kit%' DESC
                        ")
                        ->orderBy('fnfi_type')
                        ->get();
        // Return a response with the retrieved data
        return response()->json(['data' => $fnfis], 200);
    }
    public function update(Request $request, $uuid)
    {
        // Validate the incoming request data
        $request->validate([
            'fnfi_name'  => 'required|string|max:255',
            'item_price' => 'required|numeric',
            'fnfi_type'  => 'required|string|max:255'
        ]);

        // Find the item by its UUID
        $fnfi = Fnfis::findOrFail($uuid);

        // Update the record with new data
        $fnfi->update([
            'fnfi_name'   => $request->fnfi_name,
            'description' => $request->description,
            'item_price'  => $request->item_price,
            'fnfi_type'   => $request->fnfi_type
        ]);

        // Return a successful response
        return response()->json(['message' => 'FNFI updated successfully!', 'data' => $fnfi], 200);
    }
}
