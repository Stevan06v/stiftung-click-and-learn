<?php

namespace App\Filament\Resources\CompanyAdvisorResource\RelationManagers;

use App\Models\Company;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class CompanyRelationManager extends RelationManager
{
	protected static string $relationship = 'company';

	public static function getTitle(Model $ownerRecord, string $pageClass): string
	{
		return __('messages.company_model');
	}

	public function form(Form $form): Form
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

	public function table(Table $table): Table
	{
		return $table
			->recordTitleAttribute('companyname1')
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
					->url(fn($record) => "mailto:{$record->email}"),])
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
			->headerActions([
			])
			->actions([
				Tables\Actions\EditAction::make(),
				Tables\Actions\DeleteAction::make(),
			])
			->bulkActions([
				Tables\Actions\BulkActionGroup::make([
					Tables\Actions\DeleteBulkAction::make(),
				]),
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
						->withFilename('Unternehmen' . "_" . date("Y-m-d"))
				])
			])
			->emptyStateActions([]);
	}
}
