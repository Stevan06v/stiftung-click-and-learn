<?php

namespace App\Filament\Resources\ParticipantResource\RelationManagers;

use App\Filament\Resources\AmsAdvisorResource;
use App\Filament\Resources\CompanyAdvisorResource;
use App\Filament\Resources\CompanyResource;
use App\Filament\Resources\NoteResource;
use App\Models\AmsAdvisor;
use App\Models\Company;
use App\Models\CompanyAdvisor;
use App\Models\Note;
use Faker\Provider\Text;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CompanyAdvisorsRelationManager extends RelationManager
{
	protected static string $relationship = 'company_advisor';

	public static function getTitle(Model $ownerRecord, string $pageClass): string
	{
		return __('messages.company_advisor_model');
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
									->rules('nullable', 'string', 'max:50'),
								TextInput::make('firstname')->label(__('messages.firstname'))
									->rules('string', 'max:50')
									->required(),
								TextInput::make('lastname')->label(__('messages.lastname'))
									->rules('string', 'max:50')
									->required()
							])
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
								Select::make('function')->label(__('messages.function'))
									->options([
										'0' => __('messages.foundation_advisor'),
										'1' => __('messages.foundation_advisor_deputy'),
										'2' => __('messages.advisor'),
										'3' => __('messages.rehaadvisor'),
										'4' => __('messages.outplacement'),
										'5' => __('messages.outplacement_stv'),
									])
									->required(),
								Select::make('company_id')->label(__('messages.company_model'))
									->options(
										Company::all()->pluck('companyname1', 'id')
									),
								CheckBox::make('department_head')
									->label(__('messages.department_head')),
							])
					])
			]);
	}

	public function table(Table $table): Table
	{
		return $table
			->recordTitleAttribute('firstname')
			->columns([
			/*	TextColumn::make('lastname')
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
					->label(__('messages.phone_number'))
					->icon('fas-phone-volume')
					->url(fn($record) => "tel:{$record->phone_number}"),
			*/
				TextColumn::make('email')
					->sortable()
					->searchable()
					->placeholder('none')
					->alignLeft()
					->label(__('messages.email'))
					->icon('fas-envelope')
					->url(fn($record) => "mailto:{$record->email}"),
				TextColumn::make('company.companyname1')
				->label(__('messages.companyname1')),
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
				Tables\Actions\ViewAction::make(),
				Tables\Actions\EditAction::make(),
				Tables\Actions\DeleteAction::make(),

				//Tables\Actions\Action::make('bearbeiten')->url(fn (CompanyAdvisor $record): string => CompanyAdvisorResource::getUrl('edit', ['record' => $record])),

			])
			->bulkActions([
				Tables\Actions\BulkActionGroup::make([
					Tables\Actions\DeleteBulkAction::make(),
				]),
			])
			->emptyStateActions([
				Tables\Actions\CreateAction::make(),
			]);
	}
}
