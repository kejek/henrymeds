<?php

namespace App\Http\Transformers;

use App\Models\Schedule;
use Illuminate\Support\Collection;

class ScheduleTransformer 
{
	public function transform(Schedule $schedule): array
	{
		return [
			'uuid' => $schedule->uuid,
			'start' => $schedule->start_time,
			'end' => $schedule->end_time,
			'filled' => $schedule->filled,
		];
	}

	public function transformMany(Collection $schedules): array
	{
		$scheduleArray = [];

		foreach ($schedules as $schedule) {
			$scheduleArray[] = $this->transform($schedule);
		}

		return $scheduleArray;
	}
}