<?php

namespace App\Filament\Resources\NoteResource\Pages;

use App\Filament\Resources\NoteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNote extends EditRecord
{
    protected static string $resource = NoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
	protected function getActions(): array
	{
		return [
			Actions\DeleteAction::make(),
		];
	}
	protected function mutateFormDataBeforeSave(array $data): array
	{
		$data['user_id'] = auth()->id();

		return $data;
	}
}
