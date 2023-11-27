<?php

namespace App\Filament\Widgets\Dashboard;

use App\Filament\Resources\CompanyResource;
use App\Filament\Resources\NoteResource;
use App\Models\Company;
use App\Models\Contribution;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class CompanyStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
			Stat::make('Anzahl der Firmen', Company::count()),
			Stat::make('Kooperationspartner',
				DB::table('companies')
					->where('cooperation_agreement', 1)
					->count()),
			Stat::make('Keine Kooperationspartner',
				DB::table('companies')
					->where('cooperation_agreement', 0)
					->count()),

        ];
		//NoteResource::getEloquentQuery()->latest()
    }
}
