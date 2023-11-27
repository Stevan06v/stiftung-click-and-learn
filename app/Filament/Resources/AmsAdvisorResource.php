<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AmsAdvisorResource\Pages;
use App\Filament\Resources\AmsAdvisorResource\RelationManagers;
use App\Models\AmsAdvisor;
use App\Models\AmsRgs;
use App\Models\Company;
use Filament\Forms;
use Filament\Infolists\Components;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
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


class AmsAdvisorResource extends Resource
{
	protected static ?string $model = AmsAdvisor::class;

	protected static ?string $navigationIcon = 'far-comments';

	protected static ?string $recordTitleAttribute =  "AMS Berater";

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

			'ams_rgs.name',
			'ams_rgs.street',
			'ams_rgs.postcode',
			'ams_rgs.email',
			'ams_rgs.city',
			'ams_rgs.phone_number',

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
		return __('messages.ams_advisor_model');
	}

	public static function getPluralModelLabel(): string
	{
		return __('messages.ams_advisor_model');
	}

	public static function getNavigationLabel(): string
	{
		return __('messages.ams_advisor_navLabel');
	}

	protected static ?string $navigationGroup = 'DATEN';

	protected static ?int $navigationSort = 1;

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
								Select::make('salutation')->label(__('messages.salutation'))
									->options([
										'0' => __('messages.mr'),
										'1' => __('messages.mrs'),
									])
									->required(),
								TextInput::make('title')->label(__('messages.title'))
									->default('-')
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

								Select::make('ams_rgs_id')
									->label('AMS-RGS')
									->options(
										AmsRgs::all()
											->pluck('name') // id
									)->searchable()
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
									->searchable()
									->required(),
							]),
						CheckBox::make('department_head')->label(__('messages.department_head')),
						RichEditor::make('note')->label(__('messages.note'))
							->rules('nullable', 'string', 'max:255'),
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
					->icon('fas-phone-volume')
					->label(__('messages.phone_number'))
					->url(fn($record) => "tel:{$record->phone_number}"),
				TextColumn::make('email')
					->sortable()
					->searchable()
					->placeholder('none')
					->alignLeft()
					->icon('fas-envelope')
					->label(__('messages.email'))
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
				TernaryFilter::make('salutation')
					->attribute('salutation')
					->label(__('messages.salutation')),
				SelectFilter::make('title')
					->label(__('messages.title'))
					->options(
						array_unique(
							AmsAdvisor::all()
								->pluck('title', 'title')
								->toArray()
						)
					),
				TernaryFilter::make('department_head')
					->label(__('messages.department_head'))
					->attribute('department_head'),
				SelectFilter::make('function')
					->label(__('messages.function'))
					->options([
						'0' => __('messages.foundation_advisor'),
						'1' => __('messages.foundation_advisor_deputy'),
						'2' => __('messages.advisor'),
						'3' => __('messages.rehaadvisor'),
						'4' => __('messages.outplacement'),
						'5' => __('messages.outplacement_stv'),
					]),
				SelectFilter::make('ams_rgs_id')
					->label(__('messages.ams_rgs_model'))
					->options(
						array_unique(
							AmsRgs::all()
								->pluck('name', 'id')
								->toArray()
						)
					)->searchable(),
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

						Column::make('region')
							->heading(__('messages.region')),

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
				Components\TextEntry::make('title'),
				Components\TextEntry::make('firstname'),
				Components\TextEntry::make('lastname')
					->columnSpanFull(),
			]);
	}

	public static function getRelations(): array
	{
		return [
			RelationManagers\AmsRgsRelationManager::class
		];
	}

	public static function getNavigationBadge(): ?string
	{
		return static::$model::count();
	}

	public static function getPages(): array
	{
		return [
			'index' => Pages\ListAmsAdvisors::route('/'),
			'create' => Pages\CreateAmsAdvisor::route('/create'),
			'edit' => Pages\EditAmsAdvisor::route('/{record}/edit'),
			//'view' => Pages\ViewAmsAdvisor::route('/{record}'),
		];
	}
}
