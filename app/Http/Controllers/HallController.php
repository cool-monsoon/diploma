<?php

namespace App\Http\Controllers;

use App\Models\Hall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class HallController extends Controller
{
    public function index($hallId)
    {
        $hall = Hall::find($hallId);
        if ($hall) {
            $isActive = $hall->is_active;
            return response()->json(['is_active' => $isActive]);
        } else {
            return response()->json(['error' => 'Hall not found'], 404);
        }
    }

    public function show($id)
    {
        $hall = Hall::findOrFail($id);
        return response()->json($hall);
    }

    public function activateHall($hallId)
    {
        $hall = Hall::find($hallId);
        if ($hall) {
            $hall->is_active = !$hall->is_active;
            $hall->save();
            return response()->json(['message' => 'is_active value updated successfully']);
        } else {
            return response()->json(['error' => 'Hall not found'], 404);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:halls',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        Hall::create(['name' => $request->name]);

        return redirect()->route('admin');
    }


    public function updateDimentions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hall_id' => 'required|exists:halls,id',
            'rows_number' => 'required|integer|min:1',
            'seats_number' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $hallId = $request->input('hall_id');
        $rows_number = $request->input('rows_number');
        $seats_number = $request->input('seats_number');
        $hall = Hall::find($hallId);
        $hall->rows_number = $rows_number;
        $hall->seats_number = $seats_number;
        $hall->save();

        return response()->json(['message' => 'Hall updated successfully']);
    }

    public function updatePrice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hall_id' => 'required|exists:halls,id',
            'standard_seat_price' => 'required|numeric|min:0',
            'vip_seat_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $hallId = $request->input('hall_id');
        $standard_seat_price = $request->input('standard_seat_price');
        $vip_seat_price = $request->input('vip_seat_price');
        $hall = Hall::find($hallId);
        $hall->standard_seat_price = $standard_seat_price;
        $hall->vip_seat_price = $vip_seat_price;
        $hall->save();

        return response()->json(['message' => 'Hall updated successfully']);
    }

    public function checkSeatsAndShows(Request $request)
    {
        try {
            $hallId = $request->input('hallId');
            $hall = Hall::find($hallId);
            $seatsAvailable = DB::table('seats')->where('hall_id', $hallId)->exists();
            $showsAvailable = DB::table('shows')->where('hall_id', $hallId)->exists();
            $isActive = $hall->is_active;
            return response()->json(['seatsAvailable' => $seatsAvailable, 'showsAvailable' => $showsAvailable, 'is_active' => $isActive,]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $hall_id = Hall::find($id);
        if ($hall_id) {
            $hall_id->delete();
            return redirect('admin');
        }
        return response('hall not found');
    }

    public function getPrice($id)
    {
        $hall = Hall::find($id);
        if ($hall) {
            return response()->json([
                'standard_seat_price' => $hall->standard_seat_price,
                'vip_seat_price' => $hall->vip_seat_price,
            ]);
        } else {
            return response()->json(['error' => 'Hall not found'], 404);
        }
    }
}
