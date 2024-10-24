<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hall;
use App\Models\Movie;
use App\Models\Show;
use App\Models\Seat;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClientController extends Controller
{
    protected $showtimePeriod;

    public function index(Request $request)
    {
        $selectedDate = $request->query('date') ? Carbon::parse($request->query('date')) : Carbon::today();
        \Log::info("Selected date: " . $selectedDate->toDateString());

        $this->showtimePeriod = [];
        for ($i = 0; $i < 7; $i++) {
            $day = $selectedDate->copy()->addDays($i);
            $this->showtimePeriod[] = [
                'date' => $day->toDateString(), // YYYY-MM-DD
                'weekday' => $day->translatedFormat('D'),
                'mday' => $day->day,
                'class' => $this->getDayClass($day, $selectedDate)
            ];

            \Log::info("Day {$i}: " . json_encode($this->showtimePeriod[$i]));
        }

        $halls = Hall::query()->where(['is_active' => true])->get();
        $movies = Movie::whereHas('shows', function ($query) use ($selectedDate) {
            $query->whereDate('show_time', $selectedDate);
        })->with('shows.hall')->get();
        $shows = Show::all();
        \Log::info("Entire showtimePeriod: " . json_encode($this->showtimePeriod));

        return view('client.index', [
            'halls' => $halls,
            'movies' => $movies,
            'shows' => $shows,
            'showtimePeriod' => $this->showtimePeriod,
            'selectedDate' => $selectedDate,
        ]);
    }

    protected function getDayClass($day, $selectedDate)
    {
        $classes = [];
        if ($day->isToday()) {
            $classes[] = 'page-nav__day_today';
        }
        if ($day->isSameDay($selectedDate)) {
            $classes[] = 'page-nav__day_chosen';
        }
        if ($day->isWeekend()) {
            $classes[] = 'page-nav__day_weekend';
        }
        \Log::info("Classes for {$day->toDateString()}: " . implode(' ', $classes));

        return implode(' ', $classes);
    }

    public function getContent(Request $request)
    {
        $date = $request->input('date', now()->toDateString());
        $carbonDate = \Carbon\Carbon::parse($date);
        $shows = Show::where('date', $date)
            ->whereHas('hall', function ($query) {
                $query->where('is_active', true);
            })
            ->with(['movie', 'hall'])
            ->get()
            ->groupBy('movie_id');

        return view('client.components.ShowList', compact('shows', 'carbonDate'));
    }
}
