<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Show;
use App\Models\Movie;
use App\Models\Hall;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use DateTime;
use DateInterval;
use Illuminate\Support\Facades\Log;

class ShowController extends Controller
{
    public function store(Request $request)
    {
        \Log::info('Request Data:', $request->all());
        $movie = Movie::find($request->input('movie_id'));
        $validator = Validator::make($request->all(), [
            'hall_id' => 'required|exists:halls,id',
            'movie_id' => 'required|exists:movies,id',
            'date' => 'required|date_format:Y-m-d|after_or_equal:yesterday',
            'start_time' => [
                'required',
                'date_format:H:i',
                function ($attribute, $value, $fail) use ($request, $movie) {
                    $startTime = \DateTime::createFromFormat('H:i', $value);
                    $date = \DateTime::createFromFormat('Y-m-d', $request->input('date'));
                    \Log::info('Request Date: ' . $date->format('Y-m-d'));
                    if (! $startTime) {
                        $fail('Invalid start time format');
                        return;
                    }
                    if (!$date) {
                        $fail('Invalid date format');
                        return;
                    }
                    $checkingDate = $request->input('date');
                    $movieDuration = (int)$movie->duration;

                    if ($this->checkForConflictingShows($request->hall_id, $checkingDate, $startTime, $movieDuration)) {
                        $fail('Время начала сеанса конфликтует с другими сеансами');
                    }
                },
            ],
        ], [
            'start_time.required' => 'Время начала сеанса не заполнено',
            'start_time.date_format' => 'Введите время сеанса в формате 00:00',
            'start_time.0' => 'Время начала сеанса конфликтует с другими сеансами',
            'date.required' => 'Дата сеанса не заполнена',
            'date.date_format' => 'Введите дату сеанса в формате YYYY-MM-DD',
            'date.after_or_equal:today' => 'Дата сеанса истекла',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $show = new Show();
        $show->hall_id = $request->hall_id;
        $show->movie_id = $request->movie_id;
        $show->date = $request->input('date');
        $startTime = \DateTime::createFromFormat('H:i', $request->input('start_time'));
        $show->start_time = $startTime->format('H:i');
        $endTime = clone $startTime;
        $endTime->add(new \DateInterval('PT' . $movie->duration . 'M'));
        $show->end_time = $endTime->format('H:i');
        $show->save();

        return redirect('admin');
    }

    public function checkForConflictingShows($hallId, $checkingDate, $startTime, $movieDuration)
    {
        \Log::info("Checking for conflicts in hall: $hallId, for movie duration: $movieDuration");

        if (!is_string($checkingDate)) {
            return false;
        }


        $checkingDateTime = \DateTime::createFromFormat('Y-m-d', "{$checkingDate}");
        if (!$checkingDateTime) {
            return false;
        }

        $startDateTime = \DateTime::createFromFormat('Y-m-d H:i', "{$checkingDateTime->format('Y-m-d')} {$startTime->format('H:i')}");
        $endDateTime = clone $startDateTime;
        $endDateTime->add(new \DateInterval("PT{$movieDuration}M"));

        if ($startDateTime->format('Y-m-d') === $endDateTime->format('Y-m-d')) {
            \Log::info("passing to checkSameDayConflicts: {$startDateTime->format('Y-m-d H:i')}, End: {$endDateTime->format('Y-m-d H:i')}");
            return $this->checkSameDayConflicts($hallId, $startDateTime, $endDateTime);
        } else {
            \Log::info("passing to checkMultiDayConflicts: {$startDateTime->format('Y-m-d H:i')}, End: {$endDateTime->format('Y-m-d H:i')}");
            return $this->checkMultiDayConflicts($hallId, $startDateTime, $endDateTime, $movieDuration);
        }
    }

    public function checkSameDayConflicts($hallId, $startDateTime, $endDateTime)
    {
        \Log::info("entering checkSameDayConflicts: {$startDateTime->format('Y-m-d H:i')}, End: {$endDateTime->format('Y-m-d H:i')}");
        $endTimeString = $endDateTime->format('H:i');
        $startTimeString = $startDateTime->format('H:i');
        $checkingDateTime = clone $startDateTime;

        $existingShows = Show::where('hall_id', $hallId)
            ->where(function ($query) use ($startDateTime, $endTimeString, $checkingDateTime) {
                $query->where(function ($query) use ($startDateTime, $endTimeString, $checkingDateTime) {
                    $query->where('date', $checkingDateTime->format('Y-m-d'))
                        ->where('start_time', '<=', $startDateTime->format('H:i'))
                        ->where('end_time', '>=', $endTimeString);
                })->orWhere(function ($query) use ($startDateTime, $endTimeString, $checkingDateTime) {
                    $query->where('date', $checkingDateTime->format('Y-m-d'))
                        ->where('start_time', '<', $endTimeString)
                        ->where('end_time', '>', $startDateTime->format('H:i'));
                });
            })->exists();

        if ($existingShows) {
            \Log::info('Conflicting show found');
        } else {
            \Log::info('No conflicts detected with existing shows.');
        }
        return $existingShows;
    }

    public function checkMultiDayConflicts($hallId, $startDateTime, $endDateTime, $movieDuration)
    {
        \Log::info("Entering checkMultiDayConflicts: hallId: $hallId, {$startDateTime->format('Y-m-d H:i')}, End: {$endDateTime->format('Y-m-d H:i')}");

        $startTimeString = $startDateTime->format('H:i');
        $startDate = $startDateTime->format('Y-m-d');
        $checkingStartDateStartTime = clone $startDateTime;
        \Log::info("checkingStartDateStartTime: {$checkingStartDateStartTime->format('Y-m-d')}");

        $endTimeString = $endDateTime->format('H:i');
        $endDate = $endDateTime->format('Y-m-d');
        $checkingEndDateEndTime = clone $endDateTime;
        \Log::info("checkingEndDateEndTime: {$checkingEndDateEndTime->format('Y-m-d')}");

        $latestShow = Show::where('hall_id', $hallId)
            ->where('date', $checkingStartDateStartTime->format('Y-m-d'))
            ->latest('start_time')
            ->first();
        \Log::info("latest existing show start time is : $latestShow");

        $midnightBreaker = false;
        \Log::info("midnightBreaker is announced");

        if ($latestShow) {
            $latestShowStartDate =  \DateTime::createFromFormat('Y-m-d', "{$latestShow->date}");
            $latestShowStartTime =  \DateTime::createFromFormat('H:i', "{$latestShow->start_time}");
            $latestShowStartDateTime = new DateTime($latestShowStartDate->format('Y-m-d') . ' ' . $latestShowStartTime->format('H:i'));
            $startDay = $latestShowStartDateTime->format('Y-m-d');
            $latestShowEndDateTime = clone $latestShowStartDateTime;
            $latestShowEndDateTime->add(new \DateInterval("PT{$movieDuration}M"));
            $endDay = $latestShowEndDateTime->format('Y-m-d');

            if ($startDay !== $endDay) {
                \Log::info("midnightBreaker is true");
                $midnightBreaker = true;
            } else {
                \Log::info("midnightBreaker is false");
                $midnightBreaker = false;
            }
        }


        $earliestShow = Show::where('hall_id', $hallId)
            ->where('date', $checkingEndDateEndTime->format('Y-m-d'))
            ->orderBy('start_time', 'asc') // Sort by start time in ascending order
            ->first(); // Get the latest show first

        if (!$midnightBreaker) {
            \Log::info("no midnightBreaker found");
            if ($latestShow) {
                \Log::info("shows found for day of start");
                if ($earliestShow) {
                    \Log::info("shows found for day of start and day of end");
                    if ($startTimeString >= $latestShow->start_time && $endTimeString <= $earliestShow->start_time) {
                        $existingShows = false;
                        \Log::info("no overlaps with shows for day of start and day of end");
                    } else {
                        $existingShows = true;
                        \Log::info("overlaps with shows for day of start and day of end");
                    }
                } else {
                    \Log::info("shows found for day of start and not found for day of end");
                    if ($startTimeString >= $latestShow->start_time) {
                        $existingShows = false;
                        \Log::info("no overlaps with shows for day of start ");
                    } else {
                        $existingShows = true;
                        \Log::info("overlaps with shows for day of start ");
                    }
                }
            } else {
                \Log::info("shows not found for day of start");
                if ($earliestShow) {
                    \Log::info("shows not found for day of start and found day of end");
                    if ($endTimeString <= $earliestShow->start_time) {
                        $existingShows = false;
                        \Log::info("no overlaps with shows for day of end");
                    } else {
                        $existingShows = true;
                        \Log::info("overlaps with shows for day of end");
                    }
                } else {
                    \Log::info("shows not found for day of start and not found day of end");
                    $existingShows = false;
                }
            }
        } else {
            \Log::info("midnightBreaker found");
            $existingShows = true;
        }
        if ($existingShows) {
            \Log::info('Conflicting show found');
        } else {
            \Log::info('No conflicts detected with existing shows.');
        }

        return $existingShows;
    }

    public function getShowSeats($showId)
    {
        $show = Show::findOrFail($showId);
        $hall = $show->hall;
        $seats = $hall->seats()->with(['bookings' => function ($query) use ($showId) {
            $query->where('show_id', $showId);
        }])->get()->map(function ($seat) {
            return [
                'id' => $seat->id,
                'seat_type' => $seat->seat_type,
                'row_name' => $seat->row_name,
                'seat_name' => $seat->seat_name,
                'is_booked' => $seat->bookings->isNotEmpty()
            ];
        });

        return response()->json([
            'rowsNumber' => $hall->rows_number,
            'seatsNumber' => $hall->seats_number,
            'seats' => $seats
        ]);
    }

    public function destroy(Request $request)
    {
        $show = Show::find($request['id']);
        $show->delete();
        return redirect('admin');
    }
}
