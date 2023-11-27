<?php

namespace App\Filament\Resources\AmsAdvisorResource\Pages;

use App\Filament\Resources\AmsAdvisorResource;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAmsAdvisor extends EditRecord
{
    protected static string $resource = AmsAdvisorResource::class;

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
			->body('Ein AMS-Berater wurde erfolgreich bearbeitet.')
			->success()
			->icon('fas-circle-check')
			->sendToDatabase(auth()->user());
	}
}
