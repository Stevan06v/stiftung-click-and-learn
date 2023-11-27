<?php

namespace App\Filament\Resources\PvaResource\Pages;

use App\Filament\Resources\PvaResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPvas extends ListRecords
{
    protected static string $resource = PvaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
