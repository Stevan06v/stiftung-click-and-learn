<?php

namespace App\Filament\Resources\AmsAdvisorResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class AmsRgsRelationManager extends RelationManager
{
	protected static string $relationship = 'ams_rgs';

	public static function getTitle(Model $ownerRecord, string $pageClass): string
	{
		return __('messages.ams_rgs_model');
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
									->rules('email', 'max:255'),
								TextInput::make('phone_number')
									->label(__('messages.phone_number'))
									->rules('string')
									->tel(),
							]),
						RichEditor::make('note')->label(__('messages.note'))
							->rules('nullable', 'string', 'max:255'),
					]),

			]);
	}

	public function table(Table $table): Table
	{
		return $table
			->recordTitleAttribute('name')
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
				Tables\Actions\BulkActionGroup::make([
					Tables\Actions\DeleteBulkAction::make(),
				]),
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
						->withFilename("AMS-RGS" . "_" . date("Y-m-d"))
				])
			])
			->emptyStateActions([
				Tables\Actions\CreateAction::make(),
			]);
	}
}
