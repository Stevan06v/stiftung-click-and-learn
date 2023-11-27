<?php

namespace App\Filament\Resources\CompanyAdvisorResource\Pages;

use App\Filament\Resources\CompanyAdvisorResource;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCompanyAdvisor extends CreateRecord
{
    protected static string $resource = CompanyAdvisorResource::class;

	protected function getCreatedNotification(): ?Notification
	{
		return Notification::make()
			->title('Erfolgreich erstellt')
			->body('Ein Unternehmensberater wurde erfolgreich hinzugefügt.')
			->success()
			->icon('fas-circle-check')
			->sendToDatabase(auth()->user());
	}
}
