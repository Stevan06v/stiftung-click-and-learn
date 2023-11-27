<?php

namespace App\Filament\Resources\ParticipantResource\Widgets;

use App\Models\Participant;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Database\Eloquent\Model;

class ParticipantOverview extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Card::make('test', '1'),
            Card::make('test', '2'),
            Card::make('test', '3'),
        ];
    }
}
