<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function show(int $id): Collection
    {
        $schedules = Schedule::where('provider_id', $id)->where('filled', false)->get();

        return $schedules;
    }

    public function store(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'time' => 'required|string',
        ]);

        $user = User::where('id', Auth::user()->id)->first();

        if (!$user->isClient()) {
            return response()->json(['error' => 'Only clients can schedule an appointment.']);
        }

        $time = Carbon::parse($request->time)->setTimezone('UTC');

        $available = Schedule::where([
            ['provider_id', $id],
            ['filled', false]
        ])->whereTime('start_time', '<=', $time)
        ->whereTime('end_time', '>', $time)
        ->get();

        if ($available->isEmpty()) {
            return response()->json(['error' => 'This time slot is not available!']);
        }

        $reservationExists = Reservation::where('reservation_slot', $time)->first();

        if ($reservationExists) {
            return response()->json(['error' => 'Reservation slot already exists!']);
        }

        $reservation = new Reservation([
            'provider_id' => $id,
            'client_id' => $user->client()->first()->id,
            'reservation_slot' => $time,
        ]);

        $reservation->save();

        return response()->json($reservation);
    }
}
