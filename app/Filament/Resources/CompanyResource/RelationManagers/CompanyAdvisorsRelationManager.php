<?php

namespace App\Filament\Resources\CompanyResource\RelationManagers;

use App\Models\Company;
use App\Models\CompanyAdvisor;
use App\Models\Participant;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class CompanyAdvisorsRelationManager extends RelationManager
{
	protected static string $relationship = 'companyAdvisors';

	protected static ?string $recordTitleAttribute = 'id';

	public static function getTitle(Model $ownerRecord, string $pageClass): string
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

	public function form(Form $form): Form
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
									->options([
										Company::query()
											->select([
												DB::raw("CONCAT(companyname1, ' ', companyname2) as company"),
												'id',
											])
											->where('id', $this->getOwnerRecord()->id)
											->pluck('company', 'id')
											->toArray()
									])
									->default($this->getOwnerRecord()->id)
									->disabled()
									->label(__('messages.company_model')),


								CheckBox::make('department_head')
									->label(__('messages.department_head')),
							]),

						RichEditor::make('note')
							->label(__('messages.note'))
					])
			]);
	}

	public function table(Table $table): Table
	{
		return $table
			->columns([
				TextColumn::make('id')
					->alignCenter(),
				TextColumn::make('lastname')
					->alignCenter(),
				TextColumn::make('email')
					->alignCenter()
					->url(fn($record) => "mailto:{$record->email}"),
				TextColumn::make('phone_number')
					->alignCenter()
					->url(fn($record) => "tel:{$record->phone_number}"),

			])
			->filters([
				//
			])
			->headerActions([
				Tables\Actions\CreateAction::make(),
			])
			->actions([
				Tables\Actions\EditAction::make(),
				Tables\Actions\DeleteAction::make(),
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
						->withFilename("Unternehmensberater" . "_" . date("Y-m-d"))

				])
			]);
	}
}
