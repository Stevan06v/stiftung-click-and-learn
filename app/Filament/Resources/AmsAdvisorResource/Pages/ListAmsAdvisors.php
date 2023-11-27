<?php

namespace App\Filament\Resources\AmsAdvisorResource\Pages;

use App\Filament\Resources\AmsAdvisorResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAmsAdvisors extends ListRecords
{
    protected static string $resource = AmsAdvisorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
