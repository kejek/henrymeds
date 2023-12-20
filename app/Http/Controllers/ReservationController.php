<?php

namespace App\Http\Controllers;

use App\Http\Transformers\ReservationTransformer;
use App\Models\Provider;
use App\Models\Reservation;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    protected ReservationTransformer $reservationTransformer;

    public function __construct()
    {
        $this->reservationTransformer = new ReservationTransformer();
    }

    public function index(Request $request): JsonResponse
    {
        if ($request->has('provider')) {
            $request->validate([
                'provider' => 'required|string',
            ]);

            $provider = Provider::where('uuid', $request->provider)->first();

            $reservations = Reservation::where('provider_id', $provider->id)->get();

            return response()->json($this->reservationTransformer->transformMany($reservations));
        }


        $user = User::where('id', Auth::user()->id)->first();

        if (! $user->isClient()) {
            return response()->json(['error' => 'Not Authorized'], 403);
        }

        return response()->json($this->reservationTransformer
            ->transformMany(Reservation::where('client_id', $user->client->id)->get()));
    }

    public function show(string $uuid): JsonResponse
    {
        $reservation = Reservation::where('uuid', $uuid)->first();
        return response()->json($this->reservationTransformer
            ->transform($reservation));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'slot' => 'required|string',
            'provider' => 'required|string'
        ]);

        $user = User::where('id', Auth::user()->id)->first();

        $provider = Provider::where('uuid', $request->input('provider'))->first();

        if (! $user->isClient()) {
            return response()->json(['error' => 'Not Authorized'], 403);
        }

        $slot = Carbon::parse($request->slot)->timezone($user->timezone)->setTimezone('UTC');

        $latestTimeToReserve = $slot->subDay();

        if ($latestTimeToReserve > Carbon::now()) {
            return response()->json(['error' => 'Must reserve a slot 24 hours in advance'], 409);
        }

        $available = Schedule::where([
            ['provider_id', $provider->id],
            ['filled', false],
        ])->whereTime('start_time', '<=', $slot)
            ->whereTime('end_time', '>', $slot)
            ->get();

        if ($available->isEmpty()) {
            return response()->json(['error' => 'Time slot not available for this provider'], 404);
        }

        $reservationExists = Reservation::where('reservation_slot', $slot)->where('created_at', '<=', Carbon::now()->subMinutes(30))->first();

        if ($reservationExists) {
            return response()->json(['error' => 'Reservation slot already taken'], 409);
        }

        if ($slot === $available->end_time) {
            return response()->json(['error' => 'Time slot not available for this provider'], 404);  
        }

        $reservation = new Reservation([
            'provider_id' => $provider->id,
            'client_id' => $user->client()->first()->id,
            'reservation_slot' => $slot,
        ]);

        $reservation->save();

        return response()->json($this->reservationTransformer->transform($reservation));
    }

    public function update(Request $request, string $uuid): JsonResponse
    {
        $request->validate([
            'time' => 'required_if:confirm, null|string',
            'confirm' => 'required_if:time, null|boolean',
        ]);

        $reservation = Reservation::where('uuid', $uuid)->first();

        if (! $reservation) {
            return response()->json(['error' => 'Not Found'], 404);
        }

        if ($reservation->created_at <= Carbon::now()->subMinutes(30) && !$reservation->confirmed) {
            //Reservation too old. Delete and say not found.
            $reservation->delete();

            return response()->json(['error' => 'Not Found'], 404);
        }

        if ($request->has('confirm') && !$reservation->confirmed ) {
            $reservation->confirmed = true;
            $reservation->save();
        }

        if ($request->has('time')) {
            $reservation->reservation_slot = Carbon::parse($request->time)->timezone(Auth::user()->timezone)->setTimezone('UTC');

            $reservation->save();
        }
        

        return response()->json($this->reservationTransformer->transform($reservation));
    }

    public function destroy(string $uuid): JsonResponse
    {
        $user = User::where('id', Auth::user()->id)->first();

        if (! $user->isClient()) {
            return response()->json(['error' => 'Not Authorized'], 403);
        }

        $reservation = Reservation::where('uuid', $uuid)->where('client_id', $user->client->id)->first();

        if (! $reservation) {
            return response()->json(['error' => 'Not Found'], 404);
        }

        $reservation->delete();

        return response()->json(['message' => 'success']);
    }
}
