<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AbsenceResource\Pages;
use App\Filament\Resources\AbsenceResource\RelationManagers;
use App\Models\Absence;
use App\Models\AmsAdvisor;
use App\Models\CompanyAdvisor;
use App\Models\Participant;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Filters\Filter;

use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class AbsenceResource extends Resource
{
	protected static ?string $model = Absence::class;

	protected static ?string $navigationGroup = 'BERICHTE';

	protected static ?string $navigationIcon = 'fas-file-lines';

	protected static ?int $navigationSort = 994;
	public static function getModelLabel(): string
	{
		return __('messages.absence');
	}

	public static function getPluralModelLabel(): string
	{
		return __('messages.absences');
	}

	public static function getSections(): array
	{
		return ["Kurse","LAP","Schul. Ausbildung \ Kolleg","Akademie \ FHS","Universität \ Hochschule"];
	}


	public static function form(Form $form): Form
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
									->pluck('fullname', 'id')
									->toArray()
							])
							->searchable()
							->label(__('messages.participant_model')),

						Select::make('type')
							->options([
								'Urlaub' => __('messages.nursing_exemption'),
								'Krankenstand' => __('messages.sick_leave'),
								'Pflegefreistellung' => __('messages.vacation'),
							])
							->label(__('messages.type')),

						TextInput::make('business_days')
							->numeric()
							->inputMode('decimal')
							->label(__('messages.business_days'))

					]),

					Grid::make(2)->schema([
						Datepicker::make('start_date')
							->label(__('messages.start_date'))
							->required(),
						Datepicker::make('end_date')
							->label(__('messages.end_date'))
							->required(),
					]),
					RichEditor::make('annotation')
						->label(__('messages.note'))
						->rules('nullable', 'string', 'max:255')
						->columnSpanFull(),
				]),
			]);
	}

	public static function table(Table $table): Table
	{
		return $table
			->columns([
				TextColumn::make('participant.firstname')
					->searchable()
					->placeholder('none')
					->alignLeft()
					->label(__('messages.firstname')),

				TextColumn::make('participant.lastname')
					->sortable()
					->placeholder('none')
					->alignLeft()
					->label(__('messages.lastname')),

				TextColumn::make('type')
					->sortable()
					->placeholder('none')
					->alignLeft()
					->label(__('messages.type')),

				TextColumn::make('start_date')
					->searchable()
					->placeholder('none')
					->alignLeft()
					->label(__('messages.start_date')),

				TextColumn::make('end_date')
					->searchable()
					->placeholder('none')
					->alignLeft()
					->label(__('messages.end_date')),

				TextColumn::make('business_days')
					->sortable()
					->placeholder('none')
					->alignLeft()
					->label(__('messages.business_days')),

				TextColumn::make('annotation')
					->searchable()
					->placeholder('none')
					->alignLeft()
					->label(__('messages.annotation'))
					->limit(25)
					->markdown(),
			])
			->filters([
				SelectFilter::make('participant_id')
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

				SelectFilter::make('type')
					->label(__('messages.type'))
					->options([
						'Urlaub' => __('messages.nursing_exemption'),
						'Krankenstand' => __('messages.sick_leave'),
						'Pflegefreistellung' => __('messages.vacation'),
					]),

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
							->label(__('messages.absences_end_date_until')),
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

				SelectFilter::make('business_days')
					->label(__('messages.business_days'))
					->options([
						array_unique(
							Absence::all()
								->pluck('business_days', 'business_days')
								->toArray()
						)
					])->searchable(),

			])
			->actions([
			//	Tables\Actions\EditAction::make(),
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

						Column::make('start_date')
							->heading(__('messages.start_date')),

						Column::make('end_date')
							->heading(__('messages.end_date')),

						Column::make('business_days')
							->heading(__('messages.business_days')),

						Column::make('annotation')
							->heading(__('messages.annotation')),

					])
						->withWriterType(\Maatwebsite\Excel\Excel::XLSX)
						->withFilename(self::getPluralModelLabel() . "_" . date("Y-m-d") )
				])

			])
			->emptyStateActions([
				// Tables\Actions\CreateAction::make(),
			]);
	}

	public static function getRelations(): array
	{
		return [
			//
		];
	}

	public static function getNavigationBadge(): ?string
	{
		return static::$model::count();
	}


	public static function getPages(): array
	{
		return [
			'index' => Pages\ListAbsences::route('/'),
			// 'create' => Pages\CreateAbsence::route('/create'),
			// 'edit' => Pages\EditAbsence::route('/{record}/edit'),
		];
	}
}
