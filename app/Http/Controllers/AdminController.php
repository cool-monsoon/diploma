<?php

namespace App\Http\Controllers;

use App\Models\Hall;
use App\Models\Seat;
use App\Models\Movie;
use App\Models\Show;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $halls = Hall::all();
        $seats = Seat::all();
        $movies = Movie::all();
        $shows = Show::all();
        $hallId = $request->input('hall_id', 1);
        if (!$hallId) {
            $hallId = 1;
        }

        $groupedShows = $shows->groupBy('date')->map(function ($shows, $date) {
            return $shows->groupBy('hall_id');
        });

        return view('admin.index', [
            'halls' => $halls,
            'seats' => $seats,
            'hallId' => $hallId,
            'movies' => $movies,
            'shows'  => $shows,
            'existingSeats' => $seats->where('hall_id', $hallId),
            'rows_number' => $halls->where('hall_id', $hallId),
            'seats_number' => $halls->where('hall_id', $hallId),
            'groupedShows' => $groupedShows,

        ]);
    }

    public function store(Request $request)
    {
        //
    }
}
