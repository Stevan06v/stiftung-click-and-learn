<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PvaResource\Pages;
use App\Filament\Resources\PvaResource\RelationManagers;
use App\Models\Participant;
use App\Models\Pva;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class PvaResource extends Resource
{
	protected static ?string $model = Pva::class;

	protected static ?string $navigationGroup = 'DATEN';

	protected static ?string $recordTitleAttribute =  "PVA";

	public static function getGlobalSearchResultTitle(Model $record): string
	{
		return $record->firstname . " " . $record->lastname;
	}

	public static function getGloballySearchableAttributes(): array
	{
		return [
			'title',
			'firstname',
			'lastname',
			'email',
			'phone_number',

			'region',
			'street',
			'city',
			'postcode',

			'participants.matriculation_number',
			'participants.title',
			'participants.firstname',
			'participants.lastname',

			'participants.street',
			'participants.stairs',
			'participants.email',

			'participants.phone_number',
			'participants.svnr',
			'participants.iban',

		];
	}


	public static function getModelLabel(): string
	{
		return __('messages.pva_model');
	}

	public static function getPluralModelLabel(): string
	{
		return __('messages.pva_model');
	}

	public static function getNavigationLabel(): string
	{
		return __('messages.pva_navLabel');
	}

	protected static ?string $navigationIcon = 'far-handshake';

	protected static ?int $navigationSort = 3;

	public static function form(Form $form): Form
	{
		return $form
			->schema([
				Section::make(__('messages.adress_details'))
					->schema([
						Grid::make()
							->columns(3)
							->schema([
								TextInput::make('name')
									->label('Name')
									->rules('string', 'max:100')
									->required(),
								TextInput::make('street')
									->label(__('messages.street'))
									->rules('string', 'max:100')
									->required(),
								TextInput::make('postcode')
									->label(__('messages.postcode'))
									->rules('string')
									->required(),
							]),
						Grid::make()
							->columns(2)
							->schema([
								TextInput::make('city')
									->label(__('messages.city'))
									->rules('string', 'max:100')
									->required(),
								TextInput::make('region')
									->label(__('messages.region'))
									->rules('string', 'max:100')
									->required(),
							]),

					]),

				Section::make(__('messages.personnel_data'))
					->schema([
						Grid::make()
							->columns(2)
							->schema([
								Select::make('salutation')
									->label(__('messages.salutation'))
									->options([
										'0' => __('messages.mr'),
										'1' => __('messages.mrs'),
									])
									->required(),
								TextInput::make('title')
									->label(__('messages.title'))
							]),
						Grid::make()
							->columns(2)
							->schema([
								TextInput::make('firstname')
									->label(__('messages.firstname'))
									->rules('string', 'max:50')
									->required(),
								TextInput::make('lastname')
									->label(__('messages.lastname'))
									->rules('string', 'max:50')
									->required(),
							]),
						Grid::make()
							->columns(2)
							->schema([
								TextInput::make('email')
									->required()
									->label(__('messages.email'))
									->email(),
								TextInput::make('phone_number')
									->label(__('messages.phone_number'))
									->tel(),
							]),
						RichEditor::make('note')->label(__('messages.note'))
							->rules('nullable', 'string', 'max:255'),
					]),
			]);
	}
	public static function table(Table $table): Table
	{
		return $table
			->columns([
				TextColumn::make('lastname')
					->sortable()
					->searchable()
					->placeholder('none')
					->label(__('messages.lastname')),
				TextColumn::make('city')
					->sortable()
					->searchable()
					->placeholder('none')
					->label(__('messages.city')),
				TextColumn::make('phone_number')
					->sortable()
					->searchable()
					->placeholder('none')
					->label(__('messages.phone_number'))
					->url(fn($record) => "tel:{$record->phone_number}")
					->icon('fas-phone-volume'),
				TextColumn::make('email')
					->sortable()
					->searchable()
					->placeholder('none')
					->label(__('messages.email'))
					->url(fn($record) => "mailto:{$record->email}")
					->icon('fas-envelope')
			])
			->filters([
				TernaryFilter::make('salutation')
					->attribute('salutation')
					->label(__('messages.salutation')),
				SelectFilter::make('title')
					->attribute('title')
					->label(__('messages.salutation'))
					->options([
						array_unique(
							Pva::all('title')
								->pluck('title', 'title')
								->toArray()
						)
					]), SelectFilter::make('city')
					->attribute('city')
					->searchable()
					->label(__('messages.city'))
					->options([
						array_unique(
							Pva::all()
								->pluck('city', 'city')
								->toArray()
						)
					]),
				SelectFilter::make('street')
					->attribute('street')
					->searchable()
					->label(__('messages.street'))
					->options([
						array_unique(
							Pva::all()
								->pluck('street', 'street')
								->toArray()
						)
					]),
				SelectFilter::make('postcode')
					->attribute('postcode')
					->searchable()
					->label(__('messages.postcode'))
					->options([
						array_unique(
							Pva::all()
								->pluck('postcode', 'postcode')
								->toArray()
						)]),
				SelectFilter::make('title')
					->attribute('title')
					->label(__('messages.salutation'))
					->options([
						array_unique(
							Participant::all('title')
								->pluck('title', 'title')
								->toArray()
						)
					]),
			])
			->actions([
				Tables\Actions\EditAction::make(),
				Tables\Actions\DeleteAction::make()
			])
			->bulkActions([
				Tables\Actions\DeleteBulkAction::make(),

				ExportBulkAction::make()->exports([
					ExcelExport::make()->withColumns([

						Column::make('id')
							->heading('ID'),

						Column::make('salutation')
							->getStateUsing(fn($record): string => $record->salutation == 1 ? 'JA' : 'NEIN')
							->heading(__('messages.salutation')),

						Column::make('title')
							->heading(__('messages.title')),

						Column::make('firstname')
							->heading(__('messages.firstname')),

						Column::make('lastname')
							->heading(__('messages.lastname')),

						Column::make('email')
							->heading(__('messages.email')),

						Column::make('phone_number')
							->heading(__('messages.phone_number')),

						Column::make('street')
							->heading(__('messages.street')),

						Column::make('postcode')
							->heading(__('messages.postcode')),

						Column::make('city')
							->heading(__('messages.city')),

						Column::make('note')
							->heading(__('messages.note')),

					])
						->withWriterType(\Maatwebsite\Excel\Excel::XLSX)
						->withFilename(self::getPluralModelLabel() . "_" . date("Y-m-d"))

				])
			]);
	}

	public static function getRelations(): array
	{
		return [
			//
		];
	}


	public static function getNavigationBadge(): ?string
	{
		return static::$model::count();
	}

	public static function getPages(): array
	{
		return [
			'index' => Pages\ListPvas::route('/'),
			'create' => Pages\CreatePva::route('/create'),
			'edit' => Pages\EditPva::route('/{record}/edit'),
		];
	}
}
