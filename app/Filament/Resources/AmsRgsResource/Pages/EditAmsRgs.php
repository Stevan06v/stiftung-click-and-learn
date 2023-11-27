<?php

namespace App\Filament\Resources\AmsRgsResource\Pages;

use App\Filament\Resources\AmsRgsResource;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAmsRgs extends EditRecord
{
    protected static string $resource = AmsRgsResource::class;

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
			->body('Ein AMS-RGS wurde erfolgreich bearbeitet.')
			->success()
			->icon('fas-circle-check')
			->sendToDatabase(auth()->user());
	}

}
