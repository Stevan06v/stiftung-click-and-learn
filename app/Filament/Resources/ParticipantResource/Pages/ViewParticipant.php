<?php

namespace App\Filament\Resources\ParticipantResource\Pages;

use App\Filament\Resources\ParticipantResource;
use Filament\Actions;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewParticipant extends ViewRecord
{
	protected static string $resource = ParticipantResource::class;

	protected function getActions(): array
	{
		return [
			Actions\EditAction::make(),
		];
	}

}
