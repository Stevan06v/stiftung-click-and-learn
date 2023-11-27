<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResource\Pages;
use App\Filament\Resources\CompanyResource\RelationManagers;
use App\Filament\Resources\CompanyResource\RelationManagers\CompanyAdvisorsRelationManager;
use App\Models\Company;
use App\Models\CompanyAdvisor;
use Faker\Provider\ar_EG\Text;
use Filament\Forms;
use Filament\Forms\Components\BelongsToSelect;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
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
use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Columns\Column;

class CompanyResource extends Resource
{
	protected static ?string $model = Company::class;

	protected static ?string $navigationIcon = 'far-building';

	protected static ?string $navigationGroup = 'DATEN';

	protected static ?string $recordTitleAttribute =  "Unternehmen";

	public static function getGlobalSearchResultTitle(Model $record): string
	{
		return $record->companyname1 . " " . $record->companyname2;
	}

	public static function getGloballySearchableAttributes(): array
	{
		return [
			'companyname1',
			'companyname2',
			'street',
			'city',
			'postcode',
			'email',

			'companyAdvisors.firstname',
			'companyAdvisors.lastname',
			'companyAdvisors.email',
			'companyAdvisors.phone_number',
		];
	}

	public static function getModelLabel(): string
	{
		return __('messages.company_model');
	}

	public static function getPluralModelLabel(): string
	{
		return __('messages.company_model');
	}

	public static function getNavigationLabel(): string
	{
		return __('messages.company_navLabel');
	}

	protected static ?int $navigationSort = 0;


	public static function form(Form $form): Form
	{
		return $form
			->schema([
				Section::make(__('messages.adress_details'))
					->schema([
						Grid::make()
							->columns(2)
							->schema([
								TextInput::make('companyname1')
									->label(__('messages.companyname1'))
									->rules('string', 'max:100')
									->required(),
								TextInput::make('companyname2')
									->label(__('messages.companyname2'))
									->rules('nullable', 'string', 'max:100'),
							]),
						Grid::make()
							->columns(3)
							->schema([
								TextInput::make('street')
									->label(__('messages.street'))
									->rules('string', 'max:100')
									->required(),
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
								Select::make('salutation')->label(__('messages.salutation')) // nicht sicher
								->options([
									'0' => __('messages.mr'),
									'1' => __('messages.mrs'),
								])
									->required(),
								TextInput::make('email')
									->label(__('messages.email'))
									->rules('email', 'max:255')
									->required(),
								TextInput::make('phone_number')
									->label(__('messages.phone_number'))
									->rules('string')
									->tel()
									->required(),
								TextInput::make('phone_number_mobil')
									->label(__('messages.phone_number_mobil'))
									->rules('string')
									->tel()
									->required(),
								TextInput::make('fax')
									->label('FAX')
									->rules('nullable', 'string'),
								TextInput::make('website')
									->label(__('messages.website'))
									->rules('nullable', 'url', 'max:255'),
							]),

					]),
				Section::make(__('messages.more_details'))
					->schema([
						Grid::make()
							->columns(2)
							->schema([
								TextInput::make('hour_record')
									->label(__('messages.hour_record'))
									->rules('nullable', 'string', 'max:255'),
								Checkbox::make('cooperation_agreement')
									->label(__('messages.cooperation_agreement')),
							]),
						RichEditor::make('note')
							->label(__('messages.note'))
							->rules('nullable', 'string', 'max:255'),

					])
			]);
	}


	public static function table(Table $table): Table
	{
		return $table
			->columns([
				TextColumn::make('companyname1')
					->sortable()
					->searchable()
					->placeholder('none')
					->alignLeft()
					->label(__('messages.companyname1')),
				TextColumn::make('city')
					->sortable()
					->searchable()
					->placeholder('none')
					->alignLeft()
					->label(__('messages.city')),
				TextColumn::make('phone_number')
					->sortable()
					->searchable()
					->placeholder('none')
					->alignLeft()
					->label(__('messages.phone_number'))
					->icon('fas-phone-volume')
					->url(fn($record) => "tel:{$record->phone_number}"),
				TextColumn::make('email')
					->sortable()
					->searchable()
					->placeholder('none')
					->alignLeft()
					->label(__('messages.email'))
					->icon('fas-envelope')
					->url(fn($record) => "mailto:{$record->email}"),

			])
			->filters([
				SelectFilter::make('city')
					->attribute('city')
					->searchable()
					->label(__('messages.city'))
					->options([
						array_unique(
							Company::all()
								->pluck('city', 'city')
								->toArray()
						)
					]),
				SelectFilter::make('postcode')
					->attribute('postcode')
					->searchable()
					->label(__('messages.postcode'))
					->options([
						array_unique(
							Company::all()
								->pluck('postcode', 'postcode')
								->toArray()
						)]),
				TernaryFilter::make('cooperation_agreement')
					->label(__('messages.cooperation_agreement'))
					->attribute('cooperation_agreement'),
				TernaryFilter::make('salutation')
					->label(__('messages.salutation'))
					->attribute('salutation')

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

						Column::make('companyname1')
							->heading(__('messages.companyname1')),

						Column::make('companyname2')
							->heading(__('messages.companyname2')),

						Column::make('salutation')
							->getStateUsing(fn($record): string => $record->salutation == 1 ? 'JA' : 'NEIN')
							->heading(__('messages.salutation')),

						Column::make('street')
							->heading(__('messages.street')),

						Column::make('postcode')
							->heading(__('messages.postcode')),

						Column::make('city')
							->heading(__('messages.city')),

						Column::make('phone_number')
							->heading(__('messages.phone_number')),

						Column::make('fax')
							->heading(__('messages.fax')),

						Column::make('phone_number_mobil')
							->heading(__('messages.phone_number_mobil')),

						Column::make('email')
							->heading(__('messages.email')),

						Column::make('website')
							->heading(__('messages.website')),

						Column::make('cooperation_agreement')
							->getStateUsing(fn($record): string => $record->cooperation_agreement == 1 ? 'JA' : 'NEIN')
							->heading(__('messages.cooperation_agreement')),

						Column::make('website')
							->heading(__('messages.website')),

						Column::make('note')
							->heading(__('messages.note')),

						Column::make('hour_record')
							->heading(__('messages.hour_record')),
					])
						->withWriterType(\Maatwebsite\Excel\Excel::XLSX)
						->withFilename(self::getPluralModelLabel() . "_" . date("Y-m-d"))
				])
			]);
	}

	public static function getRelations(): array
	{
		return [
			CompanyAdvisorsRelationManager::class,
		];
	}

	public static function getNavigationBadge(): ?string
	{
		return static::$model::count();
	}

	public static function getPages(): array
	{
		return [
			'index' => Pages\ListCompanies::route('/'),
			'create' => Pages\CreateCompany::route('/create'),
			'edit' => Pages\EditCompany::route('/{record}/edit'),
		];
	}
}
