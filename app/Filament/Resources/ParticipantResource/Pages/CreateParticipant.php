<?php

namespace App\Filament\Resources\ParticipantResource\Pages;

use App\Filament\Resources\ParticipantResource;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Actions\Action;

class CreateParticipant extends CreateRecord
{
	protected static string $resource = ParticipantResource::class;

	protected function getHeaderActions(): array
	{
		return [
			//Actions\LocaleSwitcher::make(),
			// ...
		];
	}

	protected function getCreatedNotification(): ?Notification
	{
		return Notification::make()
			->title('Erfolgreich erstellt')
			->body('Ein Teilnehmer wurde erfolgreich hinzugefügt.')
			->success()
			->icon('fas-circle-check')
			->sendToDatabase(auth()->user());
	}

}
