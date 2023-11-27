<?php

namespace App\Filament\Resources\ParticipantResource\RelationManagers;

use App\Filament\Resources\AmsAdvisorResource;
use App\Filament\Resources\AmsRgsResource;
use App\Filament\Resources\NoteResource;
use App\Models\AmsAdvisor;
use App\Models\AmsRgs;
use App\Models\Note;
use Faker\Provider\ar_EG\Text;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AmsAdvisorsRelationManager extends RelationManager
{
	protected static string $relationship = 'ams_advisor';

	public static function getTitle(Model $ownerRecord, string $pageClass): string
	{
		return __('messages.ams_advisor_model');
	}

	public function form(Form $form): Form
	{
		return $form
			->schema([
				Section::make(__('messages.personnel_data'))
					->schema([
						Grid::make()
							->columns(2)
							->schema([
								Select::make('salutation')->label(__('messages.salutation'))
									->options([
										'0' => __('messages.mr'),
										'1' => __('messages.mrs'),
									])
									->required(),
								TextInput::make('title')->label(__('messages.title'))
									->rules('nullable', 'string', 'max:50'),
								TextInput::make('firstname')->label(__('messages.firstname'))
									->rules('string', 'max:50')
									->required(),
								TextInput::make('lastname')->label(__('messages.lastname'))
									->rules('string', 'max:50')
									->required(),
							]),
					]),

				Section::make(__('messages.contact_details'))
					->schema([
						Grid::make()
							->columns(2)
							->schema([
								TextInput::make('email')->label(__('messages.email'))
									->rules('email', 'max:255')
									->required(),
								TextInput::make('phone_number')->label(__('messages.phone_number'))
									->rules('string')
									->tel()
									->required(),
							]),
					]),

				Section::make(__('messages.ams_advisor_details'))
					->schema([
						Grid::make()
							->columns(2)
							->schema([
								Select::make('ams_rgs_id')->label('AMS-RGS')
									->options(
										AmsRgs::all()->pluck('name') // id
									)
									->required(),
								Select::make('function')->label(__('messages.function'))
									->options([
										'0' => __('messages.foundation_advisor'),
										'1' => __('messages.foundation_advisor_deputy'),
										'2' => __('messages.advisor'),
										'3' => __('messages.rehaadvisor'),
										'4' => __('messages.outplacement'),
										'5' => __('messages.outplacement_stv'),

									])
									->required(),
							]),
						CheckBox::make('department_head')->label(__('messages.department_head')),
					])
			]);
	}

	public function table(Table $table): Table
	{
		return $table
			->recordTitleAttribute('firstname')
			->columns([
				TextColumn::make('lastname')
					->placeholder('none')
					->alignLeft()
					->label(__('messages.lastname')),
				TextColumn::make('phone_number')
					->placeholder('none')
					->alignLeft()
					->icon('fas-phone-volume')
					->label(__('messages.phone_number'))
					->url(fn($record) => "tel:{$record->phone_number}"),
				TextColumn::make('email')
					->placeholder('none')
					->alignLeft()
					->icon('fas-envelope')
					->label(__('messages.email'))
					->url(fn($record) => "mailto:{$record->email}"),
				TextColumn::make('ams_rgs.name')
					->url(fn (AmsRgs $record): string => AmsRgsResource::getUrl('edit', ['record' => $record]))
			->label(__('messages.ams_rgs_model')),
				TextColumn::make('function')
					->placeholder('none')
					->alignLeft()
					->label(__('messages.function'))
					->icon('fas-wrench')
			])
			->filters([
				//
			])
			->headerActions([
				Tables\Actions\CreateAction::make(),
			])
			->actions([
				Tables\Actions\ViewAction::make(),
				Tables\Actions\EditAction::make(),
				Tables\Actions\DeleteAction::make(),

				// Tables\Actions\Action::make('bearbeiten')->url(fn (AmsAdvisor $record): string => AmsAdvisorResource::getUrl('edit', ['record' => $record])),
			])
			->bulkActions([
				Tables\Actions\BulkActionGroup::make([
					Tables\Actions\DeleteBulkAction::make(),
				]),
			])
			->emptyStateActions([
				Tables\Actions\CreateAction::make(),
			]);
	}
}
