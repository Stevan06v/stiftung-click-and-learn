<?php

namespace App\Filament\Resources\ParticipantResource\RelationManagers;

use App\Models\Participant;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class DocumentsRelationManager extends RelationManager
{
	protected static string $relationship = 'documents';

	public static function getTitle(Model $ownerRecord, string $pageClass): string
	{
		return __('messages.documents');
	}


	public function form(Form $form): Form
	{
		return $form
			->schema([
				Section::make([
					Grid::make(3)->schema([

						Select::make('participant_id')
							->options([
								Participant::query()
									->select([
										DB::raw("CONCAT(firstname, ' ', lastname, ' (#', matriculation_number, ')') as fullname"),
										'id',
									])
									->where('id', $this->getOwnerRecord()->id)
									->pluck('fullname', 'id')
									->toArray()
							])
							->default($this->getOwnerRecord()->id)
							->disabled()
							->label(__('messages.participant_model')),


						TextInput::make('designation')
							->label(__('messages.designation')),

						Datepicker::make('date')
							->label(__('messages.date'))
							->required(),
					]),

					Grid::make(2)->schema([
						Datepicker::make('start_date')
							->label(__('messages.start_date'))
							->required(),
						Datepicker::make('end_date')
							->label(__('messages.end_date'))
							->required(),
					]),

					Grid::make(3)->schema([

						TextInput::make('training_provider')
							->label(__('messages.training_provider')),

						TextInput::make('invoice_number')
							->label(__('messages.invoice_number')),

						TextInput::make('amount')
							->prefix('€')
							->numeric()
							->label(__('messages.amount'))
							->inputMode('decimal')
					]),

					Grid::make(2)->schema([

						Checkbox::make('certificate')
							->label(__('messages.certificate')),
						Checkbox::make('referral')
							->label(__('messages.referral')),
					]),
				]),
			]);
	}

	public function table(Table $table): Table
	{
		return $table
			->recordTitleAttribute('amount')
			->columns([
			/*	TextColumn::make('participant.firstname')
					->searchable()
					->placeholder('none')
					->alignLeft()
					->label(__('messages.firstname')),

				TextColumn::make('participant.lastname')
					->sortable()
					->searchable()
					->placeholder('none')
					->alignLeft()
					->label(__('messages.lastname')),
			*/
				TextColumn::make('designation')
					->sortable()
					->placeholder('none')
					->alignLeft()
					->searchable()
					->limit(16)
					->label(__('messages.designation')),

				TextColumn::make('training_provider')
					->searchable()
					->placeholder('none')
					->alignLeft()
					->limit(16)
					->sortable()
					->label(__('messages.training_provider')),

				TextColumn::make('invoice_number')
					->searchable()
					->sortable()
					->placeholder('none')
					->alignLeft()
					->limit(16)
					->label(__('messages.invoice_number')),

				TextColumn::make('amount')
					->searchable()
					->placeholder('none')
					->alignLeft()
					->sortable()
					->label(__('messages.amount')),

				IconColumn::make('certificate')
					->label(__('messages.certificate'))
					->boolean()
					->searchable()
					->alignCenter()
					->sortable()
					->toggleable(),

				IconColumn::make('referral')
					->label(__('messages.referral'))
					->boolean()
					->alignCenter()
					->sortable()
					->toggleable(),
			])
			->filters([
				TernaryFilter::make('referral')
					->label(__('messages.referral'))
					->attribute('referral'),

				TernaryFilter::make('certificate')
					->label(__('messages.certificate'))
					->attribute('certificate'),

				Filter::make('amount')
					->form([
						Forms\Components\TextInput::make('amount_from')
							->label(__('messages.amount_from'))
							->numeric()
							->inputMode('decimal'),

						Forms\Components\TextInput::make('amount_until')
							->label(__('messages.amount_until'))
							->numeric()
							->inputMode('decimal'),
					])
					->query(function (Builder $query, array $data): Builder {
						return $query
							->when(
								$data['amount_until'],
								fn(Builder $query, $amount): Builder => $query->where('amount', '<=', $amount),
							)->when(
								$data['amount_from'],
								fn(Builder $query, $amount): Builder => $query->where('amount', '>=', $amount),
							);
					}),

				Filter::make('date')
					->form([
						Forms\Components\DatePicker::make('date_from')
							->label(__('messages.documents_from')),

						Forms\Components\DatePicker::make('date_until')
							->label(__('messages.documents_until')),
					])
					->query(function (Builder $query, array $data): Builder {
						return $query
							->when(
								$data['date_until'],
								fn(Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
							)->when(
								$data['date_from'],
								fn(Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
							);
					}),

				Filter::make('start_date')
					->form([
						Forms\Components\DatePicker::make('start_date_from')
							->label(__('messages.absences_start_date_from')),

						Forms\Components\DatePicker::make('start_date_until')
							->label(__('messages.absences_start_date_until')),
					])
					->query(function (Builder $query, array $data): Builder {
						return $query
							->when(
								$data['start_date_until'],
								fn(Builder $query, $date): Builder => $query->whereDate('start_date', '<=', $date),
							)->when(
								$data['start_date_from'],
								fn(Builder $query, $date): Builder => $query->whereDate('start_date', '>=', $date),
							);
					}),

				Filter::make('end_date')
					->form([
						Forms\Components\DatePicker::make('end_date_from')
							->label(__('messages.absences_end_date_from')),

						Forms\Components\DatePicker::make('end_date_until')
							->label(__('messages.absences_start_date_until')),
					])
					->query(function (Builder $query, array $data): Builder {
						return $query
							->when(
								$data['end_date_until'],
								fn(Builder $query, $date): Builder => $query->whereDate('start_date', '<=', $date),
							)->when(
								$data['end_date_from'],
								fn(Builder $query, $date): Builder => $query->whereDate('start_date', '>=', $date),
							);
					}),
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

						Column::make('participant.lastname')
							->heading("Teilnehmer Nachname"),

						Column::make('participant.firstname')
							->heading("Teilnehmer Vorname"),

						Column::make('participant.matriculation_number')
							->heading("Teilnehmer Matrikelnummer"),

						Column::make('participant.email')
							->heading("Teilnehmer Email"),

						Column::make('participant.phone_number')
							->heading("Teilnehmer Telefonnummer"),

						Column::make('designation')
							->heading(__('messages.designation')),

						Column::make('training_provider')
							->heading(__('messages.training_provider')),

						Column::make('start_date')
							->heading(__('messages.start_date')),

						Column::make('end_date')
							->heading(__('messages.end_date')),

						Column::make('date')
							->heading(__('messages.date')),

						Column::make('invoice_number')
							->heading(__('messages.invoice_number')),

						Column::make('amount')
							->heading(__('messages.amount')),

						Column::make('referral')
							->heading(__('messages.referral')),

						Column::make('certificate')
							->getStateUsing(fn($record): string => $record->certificate == 1 ? 'JA' : 'NEIN')
							->heading(__('messages.certificate')),

					])
						->withWriterType("Belege" . "_" . date("Y-m-d"))
				])
			])
			->emptyStateActions([
				Tables\Actions\CreateAction::make(),
			]);
	}
}
