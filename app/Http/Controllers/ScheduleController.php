<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    
    public function show(int $id): Collection
    {
        $schedules = Schedule::where('provider_id', $id)->get();

        return $schedules;
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'start_time' => 'required|string|before:end_time',
            'end_time'=>'required|string|after:start_time',
        ]);

        $user = User::where('id', Auth::user()->id)->first();

        if (!$user->isProvider()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        
        $start_time = $request->input('start_time');
        $end_time = $request->input('end_time');

        $busy = Schedule::where('provider_id', $user->provider()->first()->id)
            ->byBusy($start_time, $end_time)
            ->first();

        if ($busy) {
            return response()->json(['error' => 'There is already an existing schedule between ' . $busy->start_time . ' and ' . $busy->end_time]);
        }

        $schedule = new Schedule([
            'provider_id' => $user->provider->id,
            'start_time' => Carbon::parse($start_time)->setTimezone('UTC'),
            'end_time' => Carbon::parse($end_time)->setTimezone('UTC'),
        ]);

        $schedule->save();

        return response()->json($schedule);
    }
}
