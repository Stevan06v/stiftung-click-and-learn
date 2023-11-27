<?php

namespace App\Filament\Resources\CompanyResource\Pages;

use App\Filament\Resources\CompanyResource;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCompany extends CreateRecord
{
    protected static string $resource = CompanyResource::class;
	protected function getCreatedNotification(): ?Notification
	{
		return Notification::make()
			->title('Erfolgreich erstellt')
			->body('Ein Unternehmen wurde erfolgreich hinzugefügt.')
			->success()
			->icon('fas-circle-check')
			->sendToDatabase(auth()->user());
	}

}
