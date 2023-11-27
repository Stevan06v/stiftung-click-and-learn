<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\Participant;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class ParticipantsStats extends BaseWidget
{
	protected function getStats(): array
	{
		return [
			Stat::make('Anzahl Teilnehmer', Participant::count()),
			Stat::make('Vollständige Teilnehmer', DB::table('participants')
				->where('is_complete', 1)
				->count()),
			Stat::make('Unvollständige Teilnehmer', DB::table('participants')
				->where('is_complete', 0)
				->count())

		];
	}
}
