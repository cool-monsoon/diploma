<?php

namespace App\Http\Controllers;

use App\Models\Seat;
use App\Models\Hall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class SeatController extends Controller
{
    public function index($hallId)
    {
        $seats = Seat::where('hall_id', $hallId)->get();

        return view('seats.hall', compact('seats'));
    }

    public function store(Request $request, $hallId)
    {
        $requestData = json_decode($request->getContent(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            \Log::error('Error parsing JSON: ' . json_last_error_msg());

            return response()->json(['message' => 'Invalid JSON'], 422);
        }
        if (!isset($requestData['seats']) || !is_array($requestData['seats'])) {
            return response()->json(['message' => 'Invalid seats data'], 422);
        }

        $validator = Validator::make($requestData, [
            'seats.*.hall_id' => 'required|exists:halls,id',
            'seats.*.seat_type' => 'required',
            'seats.*.row_name' => 'required|integer',
            'seats.*.seat_name' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed'], 422);
        }
        $seats = $requestData['seats'];
        $existingSeats = Seat::where('hall_id', $hallId)->get();
        if ($existingSeats->count() > 0) {
            Seat::where('hall_id', $hallId)->delete();
        }
        foreach ($seats as $seat) {
            $hall = Hall::find($seat['hall_id']);
            if (!$hall) {
                return response()->json(['message' => 'Invalid hall ID'], 422);
            }

            Seat::create([
                'hall_id' => $seat['hall_id'],
                'seat_type' => $seat['seat_type'],
                'row_name' => $seat['row_name'],
                'seat_name' => $seat['seat_name'],
            ]);
        }

        return response()->json(['message' => 'Seats added successfully'], 200);
    }


    public function show($hallId)
    {
        try {
            $hall = Hall::with('seats')->find($hallId);
            \Log::info('Hall object:', $hall ? $hall->toArray() : []);

            if ($hall) {
                $seatsInHall = $hall->seats->map(function ($seat) {
                    return [
                        'id' => $seat->id,
                        'hall_id' => $seat->hall_id,
                        'seat_type' => $seat->seat_type,
                        'row_name' => $seat->row_name,
                        'seat_name' => $seat->seat_name
                    ];
                });

                $seatsData = [
                    'rowsNumber' => $hall->rows_number,
                    'seatsNumber' => $hall->seats_number,
                    'seats' => $seatsInHall,
                ];
                return json_encode($seatsData);
            } else {
                return json_encode([
                    'message' => 'No seats found for this hall.',
                    'debug' => [
                        'hallId' => $hallId,
                        'hall' => $hall,
                    ],
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error in show method: ' . $e->getMessage());

            return response()->json(['error' => 'An error occurred.'], 500);
        }
    }
}
