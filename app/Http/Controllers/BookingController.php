<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Show;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;


class BookingController extends Controller
{
    public function showHall(Show $show)
    {
        $show = $show->load('hall.seats', 'movie');
        $bookedSeats = Booking::where('show_id', $show->id)->pluck('seat_id')->toArray();

        return view('client.hall', compact('show', 'bookedSeats'));
    }

    public function storeSelectedSeats(Request $request)
    {
        $seatIds = explode(',', $request->input('seat_ids'));
        $showId = $request->input('show_id');
        $request->session()->put('selected_seats', $seatIds);
        $request->session()->put('show_id', $showId);

        return response()->json(['success' => true]);
    }

    public function showPayment(Request $request)
    {
        $seatIds = $request->session()->get('selected_seats');
        $showId = $request->session()->get('show_id');
        $show = Show::with('movie', 'hall')->findOrFail($showId);
        $seats = Seat::whereIn('id', $seatIds)->get();
        $bookings = collect($seatIds)->map(function ($seatId) use ($show) {
            return new Booking([
                'show_id' => $show->id,
                'seat_id' => $seatId,
                'hall_id' => $show->hall_id,
            ]);
        });
        $totalCost = $seats->sum(function ($seat) use ($show) {
            return $seat->type === 'vip' ? $show->hall->vip_seat_price : $show->hall->standard_seat_price;
        });

        return view('client.payment', compact('bookings', 'show', 'seats', 'totalCost'));
    }

    public function process(Request $request)
    {
        $showId = $request->input('show_id');
        $seatIds = explode(',', $request->input('seat_ids'));
        $confirmed = $request->input('confirmed');

        if ($confirmed) {
            $show = Show::findOrFail($showId);
            $alreadyBooked = Booking::where('show_id', $showId)
                ->whereIn('seat_id', $seatIds)
                ->exists();

            if ($alreadyBooked) {
                return redirect()->back()->with('error', 'One or more selected seats have already been booked. Please choose different seats.');
            }

            $bookings = collect($seatIds)->map(function ($seatId) use ($show) {
                return Booking::create([
                    'show_id' => $show->id,
                    'seat_id' => $seatId,
                    'hall_id' => $show->hall_id,
                    'is_booked' => true,
                ]);
            });

            $qrCode = $this->generateQRCode($bookings);
            $movieTitle = $show->movie->name;
            $seatsByRow = $bookings->map(function ($booking) {
                return [
                    'row_name' => $booking->seat->row_name,
                    'seat_name' => $booking->seat->seat_name
                ];
            })->groupBy('row_name');

            $encodedSeatsByRow = json_encode($seatsByRow);
            $hall = $show->hall->name;
            $startTime = $show->start_time;
            $request->session()->forget(['selected_seats', 'show_id']);

            return redirect()->route('booking.ticket', [
                'qrCode' => $qrCode,
                'movieTitle' => $movieTitle,
                'seatsByRow' => $encodedSeatsByRow,
                'hall' => $hall,
                'startTime' => $startTime
            ])->with('success', 'Booking successful!');
        }
        return $this->showPayment($request);
    }

    private function generateQRCode($bookings)
    {
        $bookingInfo = $bookings->map(function ($booking) {
            return "Show: {$booking->show_id}, Seat: {$booking->seat_id}";
        })->implode('; ');
        $qrCode = new QrCode($bookingInfo);
        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        return $result->getDataUri();
    }

    public function showTicket(Request $request)
    {
        $qrCode = $request->query('qrCode');
        $movieTitle = $request->query('movieTitle');
        $seatsByRow = json_decode($request->query('seatsByRow'), true);
        $hall = $request->query('hall');
        $startTime = $request->query('startTime');

        return view('client.ticket', compact('qrCode', 'movieTitle', 'seatsByRow', 'hall', 'startTime'));
    }
}
