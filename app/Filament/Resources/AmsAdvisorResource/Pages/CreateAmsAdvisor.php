<?php

namespace App\Filament\Resources\AmsAdvisorResource\Pages;

use App\Filament\Resources\AmsAdvisorResource;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAmsAdvisor extends CreateRecord
{
    protected static string $resource = AmsAdvisorResource::class;

	protected function getCreatedNotification(): ?Notification
	{
		return Notification::make()
			->title('Erfolgreich erstellt')
			->body('Ein AMS-Berater wurde erfolgreich hinzugefügt.')
			->success()
			->icon('fas-circle-check')
			->sendToDatabase(auth()->user());
	}

}
