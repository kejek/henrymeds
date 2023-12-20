<?php

namespace App\Http\Transformers;

use App\Models\Reservation;
use Illuminate\Support\Collection;

class ReservationTransformer 
{
	public function transform(Reservation $reservation): array
	{
		return [
			'uuid' => $reservation->uuid,
			'reservation' => $reservation->reservation_slot,
			'confirmed' => $reservation->confirmed,
		];
	}

	public function transformMany(Collection $reservations): array
	{
		$reservationArray = [];

		foreach ($reservations as $reservation) {
			$reservationArray[] = $this->transform($reservation);
		}

		return $reservationArray;
	}
}