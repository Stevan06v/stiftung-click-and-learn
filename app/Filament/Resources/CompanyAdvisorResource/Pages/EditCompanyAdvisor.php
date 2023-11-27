<?php

namespace App\Filament\Resources\CompanyAdvisorResource\Pages;

use App\Filament\Resources\CompanyAdvisorResource;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCompanyAdvisor extends EditRecord
{
    protected static string $resource = CompanyAdvisorResource::class;

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
			->body('Ein Unternehmensberater wurde erfolgreich bearbeitet.')
			->success()
			->icon('fas-circle-check')
			->sendToDatabase(auth()->user());
	}
}
