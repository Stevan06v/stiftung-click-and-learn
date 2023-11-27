<?php

namespace App\Filament\Resources\PvaResource\Pages;

use App\Filament\Resources\PvaResource;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePva extends CreateRecord
{
    protected static string $resource = PvaResource::class;

	protected function getCreatedNotification(): ?Notification
	{
		return Notification::make()
			->title('Erfolgreich erstellt')
			->body('Ein PVA wurde erfolgreich hinzugefügt.')
			->success()
			->icon('fas-circle-check')
			->sendToDatabase(auth()->user());
	}
}
