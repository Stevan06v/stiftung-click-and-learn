<?php

namespace App\Filament\Resources\CompanyAdvisorResource\Pages;

use App\Filament\Resources\CompanyAdvisorResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;



class ViewCompanyAdvisor extends ViewRecord
{


    protected static string $resource = CompanyAdvisorResource::class;

	protected function getActions(): array
	{
		return [
			Actions\EditAction::make(),
		];
	}
}
