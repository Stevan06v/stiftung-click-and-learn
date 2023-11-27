<?php

namespace App\Filament\Resources\AmsRgsResource\Pages;

use App\Filament\Resources\AmsRgsResource;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAmsRgs extends CreateRecord
{
    protected static string $resource = AmsRgsResource::class;

	protected function getCreatedNotification(): ?Notification
	{
		return Notification::make()
			->title('Erfolgreich erstellt')
			->body('Ein AMS-RGS wurde erfolgreich hinzugefügt.')
			->success()
			->icon('fas-circle-check')
			->sendToDatabase(auth()->user());
	}
}
