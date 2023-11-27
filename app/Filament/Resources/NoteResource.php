<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NoteResource\Pages;
use App\Filament\Resources\NoteResource\RelationManagers;
use App\Models\Note;
use App\Models\Participant;
use App\Models\User;
use Filament\Infolists\Components;

use Faker\Provider\Text;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
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
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use PHPStan\Type\Php\ArrayKeyLastDynamicReturnTypeExtension;
use Filament\Forms\Set;
use Illuminate\Support\Str;
use Filament\Tables\Filters\Filter;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class NoteResource extends Resource
{
	protected static ?string $model = Note::class;

	protected static ?string $navigationIcon = 'fas-clipboard';

	protected static ?string $navigationGroup = 'NOTIZEN';

	protected static ?int $navigationSort = 997;


	public static function getModelLabel(): string
	{
		return __('messages.note');
	}

	public static function getPluralModelLabel(): string
	{
		return __('messages.notes');
	}

	public static function getNavigationLabel(): string
	{
		return __('messages.notes');
	}


	public static function form(Form $form): Form
	{
		return $form
			->schema([
				Section::make([
					TextInput::make('title')
						->label(__('messages.title'))
						->rules('string', 'max:100')
						->required(),

					RichEditor::make('text')
						->label(__('messages.note'))
						->rules('nullable', 'string', 'max:255')
						->columnSpanFull(),

					Grid::make(2)->schema([

						Select::make('participant_id')
							->options([
								Participant::query()
									->select([
										DB::raw("CONCAT(firstname, ' ', lastname, ' (#', matriculation_number, ')') as fullname"),
										'id',
									])
									->pluck('fullname', 'id')
									->toArray()
							])
							->searchable()
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

	public static function table(Table $table): Table
	{
		return $table
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

				TextColumn::make('participant.firstname')
					->sortable()
					->placeholder('none')
					->limit(16)
					->alignLeft()
					->label(__('messages.firstname')),

				TextColumn::make('participant.lastname')
					->sortable()
					->limit(16)
					->placeholder('none')
					->alignLeft()
					->label(__('messages.lastname')),

				TextColumn::make('user.name')
					->sortable()
					->limit(16)
					->placeholder('none')
					->alignLeft()
					->label(__('messages.created_by')),

				TextColumn::make('note_files')
					->alignLeft()
					->label(__('messages.files'))
					->icon(fn($record): string => (!empty($record->note_files)) ? 'fas-check' : 'fas-x')
					->getStateUsing(fn($record): string => (empty($record->note_files)) ? 'NEIN' : 'JA')
			])
			->filters([
				SelectFilter::make('participant_id')
					->searchable()
					->attribute('participant_id')
					->label(__('messages.participant_model'))
					->options([
						array_unique(
							Participant::query()
								->select([
									DB::raw("CONCAT(firstname, ' ', lastname) as fullname"),
									'id',
								])
								->pluck('fullname', 'id')
								->toArray()
						),
					]),
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

				SelectFilter::make('note_date')
					->searchable()
					->attribute('note_date')
					->label(__('messages.note_date'))
					->options([
						array_unique(
							Note::all('note_date')
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

			])
			->actions([
				Tables\Actions\ViewAction::make(),
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
						->withFilename(self::getPluralModelLabel() . "_" . date("Y-m-d"))
				])

			])
			->headerActions([

			])
			->emptyStateActions([

			]);
	}

	public static function infolist(Infolist $infolist): Infolist
	{
		return $infolist
			->schema([
				Components\Section::make([
					TextEntry::make('title')->label(__('messages.title')),
					TextEntry::make('text')
						->limit(50)
						->markdown(true)->label(__('messages.text')),
					TextEntry::make('note_date')->label(__('messages.note_date')),
					TextEntry::make('participant.firstname')
						->label(__('messages.firstname')),
					TextEntry::make('participant.lastname')
						->label(__('messages.lastname')),
					TextEntry::make('user.name')->label(__('messages.created_by')),
				])->columns(2)
			]);
	}

	public static function getNavigationBadge(): ?string
	{
		return static::$model::count();
	}

	public static function getRelations(): array
	{
		return [
			//
		];
	}

	public static function getPages(): array
	{
		return [
			'index' => Pages\ListNotes::route('/'),
			'create' => Pages\CreateNote::route('/create'),
			//'edit' => Pages\EditNote::route('/{record}/edit'),
		];
	}
}
