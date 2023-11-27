<?php

namespace App\Filament\Resources\CompanyAdvisorResource\Pages;

use App\Filament\Resources\CompanyAdvisorResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCompanyAdvisors extends ListRecords
{
    protected static string $resource = CompanyAdvisorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
