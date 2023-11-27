<?php

namespace App\Filament\Resources\AmsRgsResource\Pages;

use App\Filament\Resources\AmsRgsResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAmsRgs extends ListRecords
{
    protected static string $resource = AmsRgsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
