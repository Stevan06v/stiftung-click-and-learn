<?php

namespace App\Filament\Resources\AmsRgsResource\RelationManagers;

use App\Models\AmsRgs;
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
use Illuminate\Notifications\Action;
use Illuminate\Support\Facades\DB;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class AmsAdvisorsRelationManager extends RelationManager
{
	protected static string $relationship = 'amsAdvisors';

	protected static ?string $recordTitleAttribute = 'id';

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
									->default('-'),
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
										AmsRgs::all()->pluck('name') // id
									)
									->searchable()
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

	public function table(Table $table): Table
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
						->withFilename(__('messages.ams_advisor_model') . "_" . date("Y-m-d"))
				])
			]);
	}
}


