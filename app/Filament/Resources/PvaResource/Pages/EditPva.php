<?php

namespace App\Filament\Resources\PvaResource\Pages;

use App\Filament\Resources\PvaResource;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPva extends EditRecord
{
    protected static string $resource = PvaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

	protected function getSavedNotification(): ?Notification
	{
		return Notification::make()
			->title('Erfolgreich gespeichert')
			->body('Ein PVA wurde erfolgreich bearbeitet.')
			->success()
			->icon('fas-circle-check')
			->sendToDatabase(auth()->user());
	}
}
