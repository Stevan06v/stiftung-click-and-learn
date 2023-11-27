<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyAdvisorResource\Pages;
use App\Filament\Resources\CompanyAdvisorResource\RelationManagers;
use App\Filament\Resources\ParticipantResource\RelationManagers\CompanyAdvisorsRelationManager;
use App\Models\AmsAdvisor;
use App\Models\AmsRgs;
use App\Models\Company;
use App\Models\CompanyAdvisor;
use Faker\Provider\Text;
use Filament\Infolists\Components;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
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
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;

use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Columns\Column;


class CompanyAdvisorResource extends Resource
{
	protected static ?string $model = CompanyAdvisor::class;

	protected static ?string $navigationIcon = 'fas-person-chalkboard';

	protected static ?string $navigationGroup = 'DATEN';

	protected static ?string $recordTitleAttribute =  "Unternehmensberater";

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

			'company.companyname1',
			'company.companyname2',
			'company.street',
			'company.city',
			'company.postcode',
			'company.street',
			'company.email'
		];
	}

	public static function getModelLabel(): string
	{
		return "Unternehmensberater";
	}

	public static function getPluralModelLabel(): string
	{
		return "Unternehmensberater";
	}

	public static function getNavigationLabel(): string
	{
		return "Unternehmensberater";
	}

	public static function getFunctions(): array
	{
		return [
			__('messages.foundation_advisor'),
			__('messages.foundation_advisor_deputy'),
			__('messages.advisor'),
			__('messages.rehaadvisor'),
			__('messages.outplacement'),
			__('messages.outplacement_stv')
		];
	}

	public static function form(Form $form): Form
	{
		return $form
			->schema([
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
									->default('-')
									->label(__('messages.title')),
								TextInput::make('firstname')
									->label(__('messages.firstname'))
									->required(),
								TextInput::make('lastname')
									->label(__('messages.lastname'))
									->required(),
							])
					]),
				Section::make(__('messages.contact_details'))
					->schema([
						Grid::make()
							->columns(2)
							->schema([
								TextInput::make('email')
									->label(__('messages.email'))
									->email()
									->required(),
								TextInput::make('phone_number')
									->label(__('messages.phone_number'))
									->tel()
									->required(),
							]),
					]),

				Section::make(__('messages.ams_advisor_details'))
					->schema([
						Grid::make()
							->columns(2)
							->schema([
								Select::make('function')
									->label(__('messages.function'))
									->options([
										'0' => __('messages.foundation_advisor'),
										'1' => __('messages.foundation_advisor_deputy'),
										'2' => __('messages.advisor'),
										'3' => __('messages.rehaadvisor'),
										'4' => __('messages.outplacement'),
										'5' => __('messages.outplacement_stv'),
									])
									->required(),

								Select::make('company_id')
									->label(__('messages.company_model'))
									->options(
										Company::all()->pluck('companyname1', 'id')
									),

								CheckBox::make('department_head')
									->label(__('messages.department_head')),
							]),


						RichEditor::make('note')
							->label(__('messages.note'))
					])
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
					->alignLeft()
					->label(__('messages.lastname')),
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
				TextColumn::make('function')
					->sortable()
					->searchable()
					->placeholder('none')
					->alignLeft()
					->label(__('messages.function'))
					->icon('fas-wrench')
			])
			->filters([
				SelectFilter::make('function')
					->searchable()
					->label(__('messages.function'))
					->attribute('function')
					->options([
						'0' => __('messages.foundation_advisor'),
						'1' => __('messages.foundation_advisor_deputy'),
						'2' => __('messages.advisor'),
						'3' => __('messages.rehaadvisor'),
						'4' => __('messages.outplacement'),
						'5' => __('messages.outplacement_stv'),
					]),
				TernaryFilter::make('salutation')
					->label(__('messages.salutation'))
					->attribute('salutation'),
				TernaryFilter::make('department_head')
					->label(__('messages.department_head'))
					->attribute('department_head'),
				SelectFilter::make('title')
					->label(__('messages.title'))
					->options(
						array_unique(
							CompanyAdvisor::all()
								->pluck('title', 'title')
								->toArray()
						)
					),
				SelectFilter::make('company_id')
					->label(__('messages.company_model'))
					->options(
						array_unique(
							Company::all()
								->pluck('companyname1', 'id')
								->toArray()
						)
					)->searchable(),

				SelectFilter::make('ams_rgs_id')
					->label(__('messages.ams_rgs_model'))
					->options(
						array_unique(
							AmsRgs::all()
								->pluck('name', 'id')
								->toArray()
						)
					)->searchable()
			])
			->actions([
				EditAction::make(),
				Tables\Actions\DeleteAction::make(),
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

						Column::make('function')
							->getStateUsing(fn($record): string => self::getFunctions()[$record->function])
							->heading(__('messages.function')),

						Column::make('company.companyname1')
							->heading("Unternehmen 1. Firmenname"),

						Column::make('company.companyname2')
							->heading("Unternehmen 2. Firmenname"),

						Column::make('company.street')
							->heading("Unternehmen Straße"),

						Column::make('company.postcode')
							->heading("Unternehmen PLZ"),

						Column::make('company.city')
							->heading("Unternehmen Stadt"),

						Column::make('company.phone_number')
							->heading("Unternehmen Telefonnummer"),

						Column::make('company.fax')
							->heading("Unternehmen FAX"),

						Column::make('company.phone_number_mobil')
							->heading("Unternehmen Mobiltelefonnummer"),

						Column::make('company.email')
							->heading("Unternehmen Email"),

						Column::make('company.website')
							->heading("Unternehmen Website"),

						Column::make('department_head')
							->getStateUsing(fn($record): string => $record->department_head == 1 ? 'JA' : 'NEIN')
							->heading(__('messages.department_head')),

						Column::make('note')
							->heading(__('messages.note')),


					])
						->withWriterType(\Maatwebsite\Excel\Excel::XLSX)
						->withFilename(self::getPluralModelLabel() . "_" . date("Y-m-d"))

				])
			]);
	}


	public static function infolist(Infolist $infolist): Infolist
	{
		return $infolist
			->schema([

			]);
	}

	public static function getNavigationBadge(): ?string
	{
		return static::$model::count();
	}

	public static function getRelations(): array
	{
		return [
			RelationManagers\CompanyRelationManager::class
		];
	}

	public static function getPages(): array
	{
		return [
			'index' => Pages\ListCompanyAdvisors::route('/'),
			'create' => Pages\CreateCompanyAdvisor::route('/create'),
			'edit' => Pages\EditCompanyAdvisor::route('/{record}/edit'),
		];
	}
}
