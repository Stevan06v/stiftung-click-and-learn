<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\Contribution;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ContributionStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
			Stat::make('Durchschnittliche Kurskosten',
				round(Contribution::avg('course_cost'),2) . '€'),
			Stat::make('Summe der Kurskosten',
				round(Contribution::sum('course_cost'),2) . '€'),
			Stat::make('Minimale Kurskosten',
				round(Contribution::min('course_cost'),2) . '€'),
			Stat::make('Maximale Kurskosten',
				round(Contribution::max('course_cost'),2) . '€'),
        ];
    }
}
