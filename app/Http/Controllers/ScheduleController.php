<?php

namespace App\Http\Controllers;

use App\Http\Transformers\ScheduleTransformer;
use App\Models\Reservation;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    protected ScheduleTransformer $scheduleTransformer;

    public function __construct()
    {
        $this->scheduleTransformer = new ScheduleTransformer();
    }

    public function index(): JsonResponse
    {
        return response()->json($this->scheduleTransformer->transformMany(Schedule::all()));
    }

    public function show(string $uuid): Collection
    {
        $schedule = Schedule::where('uuid', $uuid)->first();

        $timeSlots = new Collection();


        $period = CarbonPeriod::create($schedule->start_time, '15 minutes', $schedule->end_time)
            ->excludeEndDate();

        foreach ($period as $date) {
            $slot = $date->format('Y-m-d h:i A');
            $busy = Reservation::where('reservation_slot', Carbon::parse($date)->timezone(Auth::user()->timezone)->setTimezone('UTC'))->where('provider_id', $id)->get();

            if (! $busy->isEmpty()) {
                continue;
            }

            $timeSlots->add($slot);
        }

        return $timeSlots;
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'start_time' => 'required|string|before:end_time',
            'end_time' => 'required|string|after:start_time',
        ]);

        $user = User::where('id', Auth::user()->id)->first();

        if (! $user->isProvider()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $start_time = $request->input('start_time');
        $end_time = $request->input('end_time');

        $busy = Schedule::where('provider_id', $user->provider()->first()->id)
            ->byBusy($start_time, $end_time)
            ->first();

        if ($busy) {
            return response()->json(['error' => 'There is already an existing schedule between '.$busy->start_time.' and '.$busy->end_time]);
        }

        $schedule = new Schedule([
            'provider_id' => $user->provider()->first()->id,
            'start_time' => Carbon::parse($start_time)->timezone(Auth::user()->timezone)->setTimezone('UTC'),
            'end_time' => Carbon::parse($end_time)->timezone(Auth::user()->timezone)->setTimezone('UTC'),
        ]);

        $schedule->save();

        return response()->json($this->scheduleTransformer->transform($schedule));
    }

    public function update(Request $request, string $uuid): JsonResponse
    {
        $request->validate([
            'start_time' => 'required|string|before:end_time',
            'end_time' => 'required|string|after:start_time',
        ]);

        $user = User::where('id', Auth::user()->id)->first();

        if (! $user->isProvider()) {
            return response()->json(['error' => 'Not Authorized'], 403);
        }

        $schedule = Schedule::where('uuid', $uuid)->first();

        if (! $schedule) {
            return response()->json(['error' => 'Schedule not found.'], 404);
        }

        $schedule->start_time = Carbon::parse($request->input('start_time'))->timezone(Auth::user()->timezone)->setTimezone('UTC');
        $schedule->end_time = Carbon::parse($request->input('end_time'))->timezone(Auth::user()->timezone)->setTimezone('UTC');

        $schedule->save();

        return response()->json($this->scheduleTransformer->transform($schedule));
    }

    public function destroy(string $uuid): JsonResponse
    {
        $user = User::where('id', Auth::user()->id)->first();

        if (! $user->isProvider()) {
            return response()->json(['error' => 'Not Authorized'], 403);
        }

        $schedule = Schedule::where('uuid', $uuid)->where('client_id', $user->provider->id)->first();

        if (! $schedule) {
            return response()->json(['error' => 'Not Found'], 404);
        }

        $schedule->delete();

        return response()->json(['message' => 'success']);
    }
}
