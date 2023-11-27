<?php

namespace App\Filament\Resources\ParticipantResource\RelationManagers;

use App\Filament\Resources\AmsAdvisorResource;
use App\Filament\Resources\NoteResource;
use App\Filament\Resources\ParticipantResource;
use App\Models\AmsAdvisor;
use App\Models\Note;
use App\Models\User;
use App\Models\Participant;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class NotesRelationManager extends RelationManager
{
	protected static string $relationship = 'notes';
	protected static ?int $navigationSort = 1;


	public static function getTitle(Model $ownerRecord, string $pageClass): string
	{
		return __('messages.notes');
	}


	public function form(Form $form): Form
	{
		return $form
			->schema([
				Section::make([
					TextInput::make('title')
						->label(__('messages.title'))
						->rules('string', 'max:100')
						->required(),

					/*TODO: Fix this so that the text is shown*/
					RichEditor::make('text')
						->label(__('messages.note'))
						->rules('nullable', 'string', 'max:255')
						->placeholder("Notizen")
						->columnSpanFull(),

					Grid::make(2)->schema([
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

						Forms\Components\Hidden::make('user_id')
							->default(
								auth()->id()
							),

						Datepicker::make('note_date')
							->label(__('messages.note_date'))
							->required(),

					]),

					Forms\Components\FileUpload::make('note_files')
						->label(__('messages.files'))
						->columns(3)
						->multiple()
						->directory('note_files')
						->downloadable()
						->reorderable()
						->storeFileNamesIn('original_filenames'),
				]),
			]);
	}

	public function table(Table $table): Table
	{
		return $table
			->recordTitleAttribute('title')
			->columns([
				TextColumn::make('title')
					->searchable()
					->placeholder('none')
					->alignLeft()
					->limit(16)
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

				TextColumn::make('user.name')
					->sortable()
					->placeholder('none')
					->alignLeft()
					->limit(16)
					->label(__('messages.created_by')),

				TextColumn::make('note_files')
					->alignLeft()
					->label(__('messages.files'))
					->icon(fn($record): string => (!empty($record->note_files)) ? 'fas-check' : 'fas-x')
					->getStateUsing(fn($record): string => (empty($record->note_files)) ? 'NEIN' : 'JA')
			])
			->filters([
				SelectFilter::make('note_date')
					->searchable()
					->attribute('note_date')
					->label(__('messages.note_date'))
					->options([
						array_unique(
							DB::table('notes')
								->where('participant_id', $this->getOwnerRecord()->id)
								->pluck('note_date', 'note_date')
								->toArray()
						)
					]),
				Filter::make('note_date')
					->form([
						Forms\Components\DatePicker::make('notes_from')
							->label(__('messages.notes_from')),

						Forms\Components\DatePicker::make('notes_until')
							->label(__('messages.notes_until')),
					])
					->query(function (Builder $query, array $data): Builder {
						return $query
							->when(
								$data['notes_until'],
								fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
							)->when(
								$data['notes_from'],
								fn(Builder $query, $date): Builder => $query->whereDate('note_date', '>=', $date),
							);
					}),
				SelectFilter::make('user_id')
					->searchable()
					->attribute('user_id')
					->label(__('messages.created_by'))
					->options([
						array_unique(
							User::all()
								->pluck('name', 'id')
								->toArray()
						)
					]),
			])
			->headerActions([
				Tables\Actions\CreateAction::make(),
				//Tables\Actions\Action::make('Erstellen')
				//->url(NoteResource::getUrl('create', ['participant_id' => $this->getOwnerRecord()->id])),
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
						->withFilename("Notizen" . "_" . date("Y-m-d"))
				])
			])
			->emptyStateActions([
				Tables\Actions\CreateAction::make(),

			]);
	}
}
