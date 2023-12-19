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
    public function index(): Collection
    {

        $user = User::where('id', Auth::user()->id)->first();

        if (! $user->isClient()) {
            return response()->json(['error' => 'Not Authorized'], 403);
        }

        return Reservation::where('client_id', $user->client->id)->get();
    }

    public function show(int $id): Collection
    {
        $schedules = Schedule::where('provider_id', $id)->where('filled', false)->get();

        return $schedules;
    }

    public function store(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'slot' => 'required|string',
        ]);

        $user = User::where('id', Auth::user()->id)->first();

        if (! $user->isClient()) {
            return response()->json(['error' => 'Not Authorized'], 403);
        }

        $slot = Carbon::parse($request->slot)->setTimezone('UTC');

        $available = Schedule::where([
            ['provider_id', $id],
            ['filled', false],
        ])->whereTime('start_time', '<=', $slot)
            ->whereTime('end_time', '>', $slot)
            ->get();

        if ($available->isEmpty()) {
            return response()->json(['error' => 'Time slot not available for this provider'], 404);
        }

        $reservationExists = Reservation::where('reservation_slot', $slot)->first();

        if ($reservationExists) {
            return response()->json(['error' => 'Reservation slot already taken'], 409);
        }

        $reservation = new Reservation([
            'provider_id' => $id,
            'client_id' => $user->client()->first()->id,
            'reservation_slot' => $slot,
        ]);

        $reservation->save();

        return response()->json($reservation);
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'time' => 'required|string',
        ]);

        $reservation = Reservation::where('id', $id)->first();

        if (! $reservation) {
            return response()->json(['error' => 'Not Found'], 404);
        }

        $reservation->reservation_slot = Carbon::parse($request->time)->setTimezone('UTC');

        $reservation->save();

        return response()->json(['message' => 'success']);
    }

    public function destroy(int $id)
    {
        $user = User::where('id', Auth::user()->id)->first();

        if (! $user->isClient()) {
            return response()->json(['error' => 'Not Authorized'], 403);
        }

        $reservation = Reservation::where('id', $id)->where('client_id', $user->client->id)->first();

        if (! $reservation) {
            return response()->json(['error' => 'Not Found'], 404);
        }

        $reservation->delete();

        return response()->json(['message' => 'success']);
    }
}
