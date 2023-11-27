<?php

namespace App\Filament\Widgets\Dashboard;

use App\Filament\Resources\NoteResource;
use App\Filament\Resources\Shop\OrderResource;
use App\Models\Note;
use App\Models\Shop\Order;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;



class LatestNotes extends BaseWidget
{
	protected int|string|array $columnSpan = 'full';


	public function table(Table $table): Table
	{
		return $table
			->heading(__('messages.latest_notes'))
			->query(
				NoteResource::getEloquentQuery()->latest()
			)
			->columns([
				TextColumn::make('title')
					->searchable()
					->placeholder('none')
					->alignLeft()
					->label(__('messages.title')),

				TextColumn::make('text')
					->searchable()
					->placeholder('none')
					->alignLeft()
					->label(__('messages.note'))
					->limit(30)
					->markdown(),

				TextColumn::make('note_date')
					->sortable()
					->placeholder('none')
					->alignLeft()
					->label(__('messages.date')),

				TextColumn::make('participant.firstname')
					->sortable()
					->placeholder('none')
					->alignLeft()
					->label(__('messages.firstname')),

				TextColumn::make('participant.lastname')
					->sortable()
					->placeholder('none')
					->alignLeft()
					->label(__('messages.lastname')),

				TextColumn::make('user.name')
					->sortable()
					->placeholder('none')
					->alignLeft()
					->label(__('messages.created_by')),

				TextColumn::make('note_files')
					->alignLeft()
					->label(__('messages.files'))
					->icon(fn($record): string => (!empty($record->note_files)) ? 'fas-check' : 'fas-x')
					->getStateUsing(fn($record): string => (empty($record->note_files)) ? 'NEIN' : 'JA')

			])->actions([
				/*Tables\Actions\Action::make('öffnen')
					->url(fn (Note $record): string => NoteResource::getUrl('edit', ['record' => $record])),
				*/
			])
			->bulkActions([
				ExportBulkAction::make()->exports([
					ExcelExport::make()->withColumns([

						Column::make('id')
							->heading('ID'),

						Column::make('type')
							->heading(__('messages.type')),

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

						Column::make('note_date')
							->heading(__('messages.note_date')),

						Column::make('user.name')
							->heading(__('messages.created_by')),

						Column::make('note_files')
							->getStateUsing(fn($record): string => !empty($record->note_files) ? 'JA' : 'NEIN')
							->heading("Anhang"),

						Column::make('text')
							->heading(__('messages.text')),

					])
						->withWriterType(\Maatwebsite\Excel\Excel::XLSX)
						->withFilename("Letzte-Notizen")
				])
			]);
	}
}

