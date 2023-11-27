<?php

namespace App\Filament\Resources\AmsAdvisorResource\Pages;

use App\Filament\Resources\AmsAdvisorResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAmsAdvisor extends ViewRecord
{
    protected static string $resource = AmsAdvisorResource::class;

	protected function getActions(): array
	{
		return [
			Actions\EditAction::make(),
		];
	}
}
