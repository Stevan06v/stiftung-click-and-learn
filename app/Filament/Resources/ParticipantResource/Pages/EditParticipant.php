<?php

namespace App\Filament\Resources\ParticipantResource\Pages;

use App\Filament\Resources\ParticipantResource;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\IconPosition;
use Illuminate\Notifications\Action;

class EditParticipant extends EditRecord
{
	protected static string $resource = ParticipantResource::class;

	protected function getHeaderActions(): array
	{
		return [
			Actions\DeleteAction::make(),
			Actions\ForceDeleteAction::make(),
			Actions\RestoreAction::make(),
		];
	}

	protected function mutateFormDataBeforeSave(array $data): array
	{
		$data['updated_from'] = auth()->id();

		return $data;
	}
		protected function getSavedNotification(): ?Notification
		{
			return Notification::make()
				->title('Erfolgreich gespeichert')
				->body('Ein Teilnehmer wurde erfolgreich bearbeitet.')
				->success()
				->icon('fas-circle-check')
				->sendToDatabase(auth()->user());
		}
}
