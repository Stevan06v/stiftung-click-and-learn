<?php

namespace App\Filament\Resources\ParticipantResource\RelationManagers;

use App\Models\Participant;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
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

class ContributionsRelationManager extends RelationManager
{
	protected static string $relationship = 'contributions';

	public static function getTitle(Model $ownerRecord, string $pageClass): string
	{
		return __('messages.contributions');
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

						TextInput::make('year')
							->numeric()
							->minValue(1900)
							->maxValue(date("Y"))
							->default(date("Y"))
							->label(__('messages.year')),

						TextInput::make('month')
							->minValue(1)
							->maxValue(12)
							->default(date("m"))
							->numeric()
							->label(__('messages.month')),
					]),

					Grid::make(3)->schema([

						TextInput::make('company_basic_contribution')
							->numeric()
							->prefix('€')
							->label(__('messages.company_basic_contribution'))
							->inputMode('decimal'),

						TextInput::make('basic_scholarship')
							->numeric()
							->prefix('€')
							->label(__('messages.basic_scholarship'))
							->inputMode('decimal'),

						Checkbox::make('attendance_list_received')
							->label(__('messages.attendance_list_received')),
					]),

					Grid::make(3)->schema([

						TextInput::make('additional_scholarship')
							->numeric()
							->prefix('€')
							->label(__('messages.additional_scholarship'))
							->inputMode('decimal'),

						TextInput::make('foundation_management')
							->numeric()
							->prefix('€')
							->label(__('messages.foundation_management'))
							->inputMode('decimal'),

						TextInput::make('course_cost')
							->numeric()
							->prefix('€')
							->label(__('messages.course_cost'))
							->inputMode('decimal')
					]),
				]),
			]);
	}

	public function table(Table $table): Table
	{
		return $table
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

				TextColumn::make('year')
					->sortable()
					->placeholder('none')
					->alignLeft()
					->searchable()
					->label(__('messages.year')),

				TextColumn::make('month')
					->searchable()
					->placeholder('none')
					->alignLeft()
					->sortable()
					->label(__('messages.month')),

				TextColumn::make('company_basic_contribution')
					->searchable()
					->sortable()
					->placeholder('none')
					->alignLeft()
					->label(__('messages.company_basic_contribution')),

				TextColumn::make('basic_scholarship')
					->searchable()
					->placeholder('none')
					->alignLeft()
					->sortable()
					->label(__('messages.basic_scholarship')),

				IconColumn::make('attendance_list_received')
					->label(__('messages.attendance_list_received'))
					->boolean()
					->searchable()
					->alignCenter()
					->sortable()
					->toggleable(),

/*
				TextColumn::make('additional_scholarship')
					->searchable()
					->placeholder('none')
					->alignLeft()
					->sortable()
					->label(__('messages.additional_scholarship')),


				TextColumn::make('foundation_management')
					->searchable()
					->placeholder('none')
					->alignLeft()
					->sortable()
					->label(__('messages.foundation_management')),
*/
				TextColumn::make('course_cost')
					->searchable()
					->placeholder('none')
					->alignLeft()
					->sortable()
					->label(__('messages.course_cost')),])
			->filters([
				TernaryFilter::make('attendance_list_received')
					->label(__('messages.attendance_list_received'))
					->attribute('attendance_list_received'),

				Filter::make('year')
					->form([
						Forms\Components\TextInput::make('contributions_from_year')
							->label(__('messages.contributions_from_year'))
							->numeric(),
						Forms\Components\TextInput::make('contributions_until_year')
							->label(__('messages.contributions_until_year'))
							->numeric()
					])
					->query(function (Builder $query, array $data): Builder {
						return $query
							->when(
								$data['contributions_until_year'],
								fn(Builder $query, $year): Builder => $query->where('year', '<=', $year),
							)->when(
								$data['contributions_from_year'],
								fn(Builder $query, $year): Builder => $query->where('year', '>=', $year),
							);
					}),
				Filter::make('month')
					->form([
						Forms\Components\TextInput::make('contributions_from_month')
							->label(__('messages.contributions_from_month'))
							->numeric(),
						Forms\Components\TextInput::make('contributions_until_month')
							->label(__('messages.contributions_until_month'))
							->numeric()
					])
					->query(function (Builder $query, array $data): Builder {
						return $query
							->when(
								$data['contributions_until_month'],
								fn(Builder $query, $month): Builder => $query->where('month', '<=', $month),
							)->when(
								$data['contributions_from_month'],
								fn(Builder $query, $month): Builder => $query->where('month', '>=', $month),
							);
					}),

				Filter::make('company_basic_contribution')
					->form([
						Forms\Components\TextInput::make('company_basic_contribution_from')
							->label(__('messages.company_basic_contribution_from'))
							->numeric(),
						Forms\Components\TextInput::make('company_basic_contribution_to')
							->label(__('messages.company_basic_contribution_to'))
							->numeric()
					])
					->query(function (Builder $query, array $data): Builder {
						return $query
							->when(
								$data['company_basic_contribution_from'],
								fn(Builder $query, $company_basic_contribution): Builder => $query->where('company_basic_contribution', '>=', $company_basic_contribution),
							)->when(
								$data['company_basic_contribution_to'],
								fn(Builder $query, $company_basic_contribution): Builder => $query->where('company_basic_contribution', '<=', $company_basic_contribution),
							);
					}),

				Filter::make('company_basic_contribution')
					->form([
						Forms\Components\TextInput::make('company_basic_contribution_from')
							->label(__('messages.company_basic_contribution_from'))
							->numeric(),
						Forms\Components\TextInput::make('company_basic_contribution_to')
							->label(__('messages.company_basic_contribution_to'))
							->numeric()
					])
					->query(function (Builder $query, array $data): Builder {
						return $query
							->when(
								$data['company_basic_contribution_from'],
								fn(Builder $query, $company_basic_contribution): Builder => $query->where('company_basic_contribution', '>=', $company_basic_contribution),
							)->when(
								$data['company_basic_contribution_to'],
								fn(Builder $query, $company_basic_contribution): Builder => $query->where('company_basic_contribution', '<=', $company_basic_contribution),
							);
					}),

				Filter::make('basic_scholarship')
					->form([
						Forms\Components\TextInput::make('basic_scholarship_from')
							->label(__('messages.basic_scholarship_from'))
							->numeric(),
						Forms\Components\TextInput::make('basic_scholarship_to')
							->label(__('messages.basic_scholarship_to'))
							->numeric()
					])
					->query(function (Builder $query, array $data): Builder {
						return $query
							->when(
								$data['basic_scholarship_from'],
								fn(Builder $query, $basic_scholarship): Builder => $query->where('basic_scholarship', '>=', $basic_scholarship),
							)->when(
								$data['basic_scholarship_to'],
								fn(Builder $query, $basic_scholarship): Builder => $query->where('basic_scholarship', '<=', $basic_scholarship),
							);
					}),

				Filter::make('additional_scholarship')
					->form([
						Forms\Components\TextInput::make('additional_scholarship_from')
							->label(__('messages.additional_scholarship_from'))
							->numeric(),
						Forms\Components\TextInput::make('additional_scholarship_to')
							->label(__('messages.additional_scholarship_to'))
							->numeric()
					])
					->query(function (Builder $query, array $data): Builder {
						return $query
							->when(
								$data['additional_scholarship_from'],
								fn(Builder $query, $additional_scholarship): Builder => $query->where('additional_scholarship', '>=', $additional_scholarship),
							)->when(
								$data['additional_scholarship_to'],
								fn(Builder $query, $additional_scholarship): Builder => $query->where('additional_scholarship', '<=', $additional_scholarship),
							);
					}),

				Filter::make('foundation_management')
					->form([
						Forms\Components\TextInput::make('foundation_management_from')
							->label(__('messages.foundation_management_from'))
							->numeric(),
						Forms\Components\TextInput::make('foundation_management_to')
							->label(__('messages.foundation_management_to'))
							->numeric()
					])
					->query(function (Builder $query, array $data): Builder {
						return $query
							->when(
								$data['foundation_management_from'],
								fn(Builder $query, $foundation_management): Builder => $query->where('foundation_management', '>=', $foundation_management),
							)->when(
								$data['foundation_management_to'],
								fn(Builder $query, $foundation_management): Builder => $query->where('foundation_management', '<=', $foundation_management),
							);
					}),
				Filter::make('course_cost')
					->form([
						Forms\Components\TextInput::make('course_cost_from')
							->label(__('messages.course_cost_from'))
							->numeric(),
						Forms\Components\TextInput::make('course_cost_to')
							->label(__('messages.course_cost_to'))
							->numeric()
					])
					->query(function (Builder $query, array $data): Builder {
						return $query
							->when(
								$data['course_cost_from'],
								fn(Builder $query, $course_cost): Builder => $query->where('course_cost', '>=', $course_cost),
							)->when(
								$data['course_cost_to'],
								fn(Builder $query, $course_cost): Builder => $query->where('course_cost', '<=', $course_cost),
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
				ExportBulkAction::make()
					->exports([
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

						Column::make('year')
							->heading(__('messages.year')),

						Column::make('month')
							->heading(__('messages.month')),

						Column::make('attendance_list_received')
							->getStateUsing(fn($record): string => $record->attendance_list_received == 1 ? 'JA' : 'NEIN')
							->heading(__('messages.attendance_list_received')),

						Column::make('company_basic_contribution')
							->heading(__('messages.company_basic_contribution')),

						Column::make('basic_scholarship')
							->heading(__('messages.basic_scholarship')),

						Column::make('additional_scholarship')
							->heading(__('messages.additional_scholarship')),

						Column::make('foundation_management')
							->heading(__('messages.foundation_management')),

						Column::make('course_cost')
							->heading(__('messages.course_cost')),

					])
						->withWriterType(\Maatwebsite\Excel\Excel::XLSX)
						->withFilename("Beiträge" . "_" . date("Y-m-d"))
				])
			])
			->emptyStateActions([
				Tables\Actions\CreateAction::make(),
			]);
	}
}
