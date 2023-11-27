<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AmsRgsResource\Pages;
use App\Filament\Resources\AmsRgsResource\RelationManagers;
use App\Filament\Resources\AmsRgsResource\RelationManagers\AmsAdvisorsRelationManager;
use App\Models\AmsAdvisor;
use App\Models\AmsRgs;
use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Filters\SelectFilter;
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
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class AmsRgsResource extends Resource
{
	protected static ?string $model = AmsRgs::class;

	protected static ?string $navigationGroup = 'DATEN';

	protected static ?string $recordTitleAttribute =  "AMS RGS";

	public static function getGlobalSearchResultTitle(Model $record): string
	{
		return $record->name;
	}

	public static function getGloballySearchableAttributes(): array
	{
		return [
			'amsAdvisors.firstname',
			'amsAdvisors.lastname',
			'amsAdvisors.email',

			'name',
			'postcode',
			'street',
			'city',
			'email',
			'phone_number',
		];
	}

	public static function getModelLabel(): string
	{
		return __('messages.ams_rgs_model');
	}

	public static function getPluralModelLabel(): string
	{
		return __('messages.ams_rgs_model');
	}

	public static function getNavigationLabel(): string
	{
		return __('messages.ams_rgs_navLabel');
	}

	protected static ?string $navigationIcon = 'fas-location-dot';

	protected static ?int $navigationSort = 2;

	public static function form(Form $form): Form
	{
		return $form
			->schema([
				Section::make(__('messages.adress_details'))
					->schema([
						Grid::make()
							->columns(2)
							->schema([
								TextInput::make('name')
									->label(__('messages.name'))
									->rules('string', 'max:50')
									->required(),
								TextInput::make('street')
									->label(__('messages.street'))
									->rules('string', 'max:100')
									->required(),
							]),
						Grid::make()
							->columns(2)
							->schema([
								TextInput::make('postcode')
									->label(__('messages.postcode'))
									->rules('string')
									->required(),
								TextInput::make('city')
									->label(__('messages.city'))
									->rules('string', 'max:100')
									->required(),
							]),
					]),

				Section::make(__('messages.contact_details'))
					->schema([
						Grid::make()
							->columns(2)
							->schema([
								TextInput::make('email')
									->label(__('messages.email'))
									->required()
									->email(),
								TextInput::make('phone_number')
									->label(__('messages.phone_number'))
									->required()
									->tel(),
							]),
						RichEditor::make('note')->label(__('messages.note'))
					]),
			]);
	}

	public static function table(Table $table): Table
	{
		return $table
			->columns([
					TextColumn::make('name')
						->sortable()
						->searchable()
						->placeholder('none')
						->alignLeft()
						->label(__('messages.name')),
					TextColumn::make('city')
						->sortable()
						->searchable()
						->placeholder('none')
						->alignLeft()
						->label(__('messages.city')),
					TextColumn::make('email')
						->sortable()
						->searchable()
						->placeholder('none')
						->alignLeft()
						->icon('fas-envelope')
						->label(__('messages.email'))
						->url(fn($record) => "mailto:{$record->email}"),
			])
			->filters([
				SelectFilter::make('postcode')
					->attribute('postcode')
					->searchable()
					->label(__('messages.postcode'))
					->options([
						array_unique(
							AmsRgs::all()
								->pluck('postcode', 'postcode')
								->toArray()
						)]),
				SelectFilter::make('city')
					->attribute('city')
					->searchable()
					->label(__('messages.city'))
					->options([
						array_unique(
							AmsRgs::all()
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
							AmsRgs::all()
								->pluck('street', 'street')
								->toArray()
						)
					])
			])
			->actions([
				EditAction::make(),
				Tables\Actions\DeleteAction::make()

			])
			->bulkActions([
				DeleteBulkAction::make(),
				ExportBulkAction::make()->exports([
					ExcelExport::make()->withColumns([

						Column::make('id')
							->heading('ID'),

						Column::make('salutation')
							->getStateUsing(fn($record): string => $record->salutation == 1 ? 'JA' : 'NEIN')
							->heading(__('messages.salutation')),

						Column::make('name')
							->heading(__('messages.name')),

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
			AmsAdvisorsRelationManager::class,
		];
	}

	public static function getNavigationBadge(): ?string
	{
		return static::$model::count();
	}

	public static function getPages(): array
	{
		return [
			'index' => Pages\ListAmsRgs::route('/'),
			'create' => Pages\CreateAmsRgs::route('/create'),
			'edit' => Pages\EditAmsRgs::route('/{record}/edit'),
		];
	}
}
