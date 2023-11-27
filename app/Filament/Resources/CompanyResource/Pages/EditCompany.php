<?php

namespace App\Filament\Resources\CompanyResource\Pages;

use App\Filament\Resources\CompanyResource;
use App\Models\Company as ModelsCompany;
use App\Models\CompanyAdvisor;
use Faker\Provider\ar_EG\Text;
use Faker\Provider\Company;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\RelationManagers\HasManyRelationManager;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class EditCompany extends EditRecord
{
    protected static string $resource = CompanyResource::class;

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
			->body('Ein Unternehmen wurde erfolgreich bearbeitet.')
			->success()
			->icon('fas-circle-check')
			->sendToDatabase(auth()->user());
	}
}
