<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParticipantResource\Pages;
use App\Filament\Resources\ParticipantResource\RelationManagers;
use App\Filament\Resources\ParticipantResource\Widgets\ParticipantOverview;
use App\Models\Activity;
use App\Models\AmsAdvisor;
use App\Models\AmsRgs;
use App\Models\Company;
use App\Models\CompanyAdvisor;
use App\Models\EducationCategory;
use App\Models\Participant;
use App\Models\Pva;
use App\Models\User;
use DanHarrin\LivewireRateLimiting\Tests\Component;
use Faker\Provider\ar_EG\Text;
use Filament\Commands\MakeBelongsToManyCommand;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Infolists\Components;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\TextInputColumn;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use PHPStan\PhpDoc\Tag\TemplateTag;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Columns\Column;


class ParticipantResource extends Resource
{

	//use Translatable;

	protected static ?string $model = Participant::class;
	protected static ?string $navigationIcon = 'fas-people-line';

	protected static ?string $recordTitleAttribute =  "Teilnehmer";

	public static function getGlobalSearchResultTitle(Model $record): string
	{
		return $record->firstname . " " . $record->lastname;
	}

	public static function getGloballySearchableAttributes(): array
	{
		return [
			'pva.firstname',
			'pva.lastname',
			'pva.email',

			'activity.name',

			'ams_advisor.firstname',
			'ams_advisor.lastname',
			'ams_advisor.email',

			'education_category.name',

			'company_advisor.firstname',
			'company_advisor.lastname',
			'company_advisor.email',

			'matriculation_number',
			'title',
			'firstname',
			'lastname',

			'street',
			'stairs',
			'email',

			'phone_number',
			'svnr',
			'iban'
		];
	}

	public static function getModelLabel(): string
	{
		return __('messages.participant_model');
	}

	public static function getPluralModelLabel(): string
	{
		return __('messages.participant_model');
	}

	public static function getNavigationLabel(): string
	{
		return __('messages.participant_navLabel');
	}

	protected static ?int $navigationSort = 0;

	public static function getSections(): array
	{
		return ["Kurse","LAP","Schul. Ausbildung \ Kolleg","Akademie \ FHS","Universität \ Hochschule"];
	}

	public static function form(Form $form): Form
	{
		$wizard = Wizard::make([
			Step::make(__('messages.personal_data'))
				->schema([
					Section::make(__('messages.personnel_data'))
						->schema([
							Grid::make()
								->columns(3)
								->schema([
									TextInput::make('matriculation_number')
										->label(__('messages.matriculation_number')),

									Select::make('section')
										->label('Section')
										->options([
											'0' => __('messages.social_worker'),
											'1' => __('messages.social_worker_plus'),
											'2' => 'Aqua',
											'3' => 'Aqua spez.',
											'4' => 'Aqua+'
										])
										->required(),

									Select::make('salutation')->label(__('messages.salutation'))
										->options([
											'0' => __('messages.mr'),
											'1' => __('messages.mrs'),
										])
										->required(),

									TextInput::make('title')
										->default('-')
										->label(__('messages.title')),

									TextInput::make('firstname')->label(__('messages.firstname'))
										->required(),

									TextInput::make('lastname')->label(__('messages.lastname'))
										->required(),
								]),
							Grid::make()
								->columns(2)
								->schema([
									Datepicker::make('birthdate')
										->label(__('messages.birthdate'))
										->required(),

									TextInput::make('svnr')
										->label('SVNr.')
										->rules('digits:10')
										->required(),

								]),
						]),
					Section::make(__('messages.contact_details'))
						->schema([
							Grid::make()
								->columns(3)
								->schema([
									TextInput::make('street')
										->label(__('messages.street'))
										->required(),
									TextInput::make('addition')
										->label(__('messages.addition')),
									TextInput::make('postcode')
										->label(__('messages.postcode'))
										->required(),
									TextInput::make('street_number')
										->label(__('messages.street_number')),
									TextInput::make('stairs')
										->label(__('messages.stairs')),
									TextInput::make('door')
										->label(__('messages.door')),
								]),
							Grid::make()
								->columns(2)
								->schema([
									TextInput::make('city')
										->label(__('messages.city'))
										->required(),
									TextInput::make('phone_number')
										->label(__('messages.phone_number'))
										->tel()
										->required(),
								]),
							Grid::make()
								->columns(2)
								->schema([
									TextInput::make('email')
										->required()
										->label(__('messages.email'))
										->required(),
									Checkbox::make('report')
										->label(__('messages.report'))
										->inline(),
								]),
						]),
					Section::make(__('messages.bank_details'))
						->columns(2)
						->schema([
							TextInput::make('iban')
								->label('IBAN')
								->rules('string', 'iban')
								->required(),
							TextInput::make('bic')
								->label('BIC')
								->required(),
						])
				]),
			Step::make('AMS & PVA')
				->schema([
					Section::make('AMS & PVA Details')
						->schema([
							Grid::make()
								->columns(2)
								->schema([
									Select::make('pva_id')
										->label('PVA')
										->searchable()
										->options(
											Pva::all()->pluck('city')
										),
									Select::make('activity_id')
										->label(__('messages.activity'))
										->searchable()
										->options(
											Activity::all()->pluck('name')
										),
									Select::make('ams_advisor_id')
										->label(__('messages.ams_advisor_model'))
										->searchable()
										->options(
											AmsAdvisor::all()
												->pluck('lastname')
										),
									Select::make('ams_status')
										->label('AMS Status')
										->options([
											'0' => 'ALG',
											'1' => __('messages.emergency'),
											'2' => 'DLU',
											'3' => 'Kein Status'
										])->default('3')
								])
						])
				]),
			Step::make(__('messages.statistics'))
				->schema([
					Section::make('Restlichen Formulare')
						->schema([
							Grid::make()
								->columns(3)
								->schema([
									Datepicker::make('entry')
										->label(__('messages.entry')),
									Datepicker::make('exit')
										->label(__('messages.exit')),
									Datepicker::make('actual_exit')
										->label(__('messages.actual_exit')),
									TextInput::make('exit_reason')
										->label(__('messages.exit_reason')),
									Datepicker::make('dv_date')
										->label(__('messages.dv_date')),
									Select::make('aw_status')
										->label('AW-Status')
										->options([
											'0' => 'K',
											'1' => 'G',
											'2' => 'U',
										]),
									Datepicker::make('aw_status_date')
										->label(__('messages.aw_status_date')),
									Checkbox::make('education_plan')
										->label(__('messages.education_plan')),
									Checkbox::make('education_plan_approved')
										->label(__('messages.education_plan_approved')),
									Checkbox::make('training_agreement')
										->label(__('messages.training_agreement')),
									Checkbox::make('entry_notification_land')
										->label(__('messages.entry_notification_land')),
									Checkbox::make('schALG_conversion')
										->label(__('messages.schALG_conversion')),
									Checkbox::make('agreement_with_company')
										->label(__('messages.agreement_with_company')),
									Datepicker::make('agreement_date')
										->label(__('messages.agreement_date')),
									Checkbox::make('land_advance')
										->label(__('messages.land_advance')),
									Checkbox::make('land_final_bill')
										->label(__('messages.land_final_bill')),
									TextInput::make('share_sign_land')
										->label(__('messages.share_sign_land')),
									TextInput::make('education_cost_plan')
										->label(__('messages.education_cost_plan')),
									Checkbox::make('subsidy_coursecost_charged')
										->label(__('messages.subsidy_coursecost_charged')),
									TextInput::make('subsidy_coursecost_amount')
										->label(__('messages.subsidy_coursecost_amount')),
									TextInput::make('land_request_ub')
										->label(__('messages.land_request_ub')),
									TextInput::make('land_request_qb')
										->label(__('messages.land_request_qb')),
									TextInput::make('land_request_educationcosts')
										->label(__('messages.land_request_educationcosts')),
									Datepicker::make('land_request_date')
										->label(__('messages.land_request_date')),
									Datepicker::make('land_request_approval_date')
										->label(__('messages.land_request_approval_date')),
									Datepicker::make('land_request_zlg_date')
										->label(__('messages.land_request_zgl_date')),
									TextInput::make('land_final_bill_amount')
										->label(__('messages.land_final_bill_amount')),
									Datepicker::make('land_final_bill_request_date')
										->label(__('messages.land_final_bill_request_date')),
									Datepicker::make('land_final_bill_approval_date')
										->label(__('messages.land_final_bill_approval_date')),
									Datepicker::make('land_final_bill_zlg_date')
										->label(__('messages.land_final_bill_zlg_date')),
									/*RichEditor::make('note')
										->label(__('messages.note'))
										->rules('nullable', 'string', 'max:255'),*/
								]),
						]),
				]),
			Wizard\Step::make(__('messages.assignment_internship'))
				->schema([
					Section::make(__('messages.career_details'))
						->schema([
							Grid::make()
								->columns(3)
								->schema([
									TextInput::make('career_goal')
										->label(__('messages.career_goal')),
									TextInput::make('last_activity')
										->label(__('messages.last_activity')),
									TextInput::make('pre_qualification')
										->label(__('messages.pre_qualification')),
								]),
							Grid::make()
								->columns(2)
								->schema([
									Select::make('education_category_id')
										->searchable()
										->label(__('messages.education_category'))
										->options(
											EducationCategory::all()
												->pluck('name')
										),
									Select::make('education_form')
										->label(__('messages.education_form'))
										->options([
											'0' => __('messages.courses'),
											'1' => 'LAP',
											'2' => __('messages.school_education'),
											'3' => __('messages.academy'),
											'4' => __('messages.university')
										]),
								])
						]),
					Section::make(__('messages.internship_details'))
						->schema([
							Grid::make()
								->columns(2)
								->schema([

									Select::make('company_advisor_id')
										->searchable()
										->label(__('messages.company_advisor_model'))
										->options(
											CompanyAdvisor::all()
												->pluck('lastname')
										),

									TextInput::make('internship_location')
										->label(__('messages.internship_location')),

									TextInput::make('vacation_entitlement')
										->label(__('messages.vacation_entitlement')),

									TextInput::make('weekly_hours')
										->label(__('messages.weekly_hours')),

									TextInput::make('entitlement_to_care_leave')
										->label(__('messages.entitlement_to_care_leave')),

									Datepicker::make('coaching_date')
										->label(__('messages.coaching_date')),
								]),
						]),
				]),
			/*Wizard\Step::make(__('messages.contributions'))
				->schema([
					// ...
				]),
			Wizard\Step::make(__('messages.documentation_entry'))
				->schema([

				]),*/
		])->skippable();

		return $form
			->schema([
				Forms\Components\Hidden::make('user_id')
					->default(
						auth()->id()
					),
				$wizard,
				Section::make([
					Toggle::make('is_complete')
						->label(__('messages.complete'))
						->default(false)
						->helperText(__('messages.is_complete_description')),

				])->heading(__('messages.is_complete')),

			])->columns(1);
	}

	public static function table(Table $table): Table
	{
		return $table
			->columns([
				TextColumn::make('lastname')
					->sortable()
					->searchable()
					->placeholder('none')
					->label(__('messages.lastname')),

				TextColumn::make('email')
					->sortable()
					->searchable()
					->placeholder('none')
					->label(__('messages.email'))
					->url(fn($record) => "mailto:{$record->email}")
					->icon('fas-envelope'),

				Tables\Columns\IconColumn::make('is_complete')
					->label(__('messages.is_complete'))
					->boolean()
					->alignCenter()
					->sortable()
					->toggleable(),

				TextColumn::make('phone_number')
					->sortable()
					->searchable()
					->placeholder('none')
					->label(__('messages.phone_number'))
					->url(fn($record) => "tel:{$record->phone_number}")
					->icon('fas-phone-volume'),

			])
			->filters([
				SelectFilter::make('section')
					->attribute('section')
					->label(__('messages.section'))
					->options([
						'0' => __('messages.courses'),
						'1' => 'LAP',
						'2' => __('messages.school_education'),
						'3' => __('messages.academy'),
						'4' => __('messages.university')
					]),

				TernaryFilter::make('salutation')
					->attribute('salutation')
					->label(__('messages.salutation')),

				SelectFilter::make('title')
					->attribute('title')
					->label(__('messages.salutation'))
					->options([
						array_unique(
							Participant::all('title')
								->pluck('title', 'title')
								->toArray()
						)
					]),
				SelectFilter::make('city')
					->attribute('city')
					->searchable()
					->label(__('messages.city'))
					->options([
						array_unique(
							Participant::all()
								->pluck('city', 'city')
								->toArray()
						)
					]),
				SelectFilter::make('street')
					->attribute('street')
					->searchable()
					->label(__('messages.street'))
					->options([
						array_unique(
							Participant::all()
								->pluck('street', 'street')
								->toArray()
						)
					]),
				SelectFilter::make('postcode')
					->attribute('postcode')
					->searchable()
					->label(__('messages.postcode'))
					->options([
						array_unique(
							Participant::all()
								->pluck('postcode', 'postcode')
								->toArray()
						)]),
				SelectFilter::make('ams_status')
					->label(__('messages.ams_status'))
					->options([
						'0' => 'ALG',
						'1' => __('messages.emergency'),
						'2' => 'DLU',
						'3' => 'Kein Status'
					]),
				TernaryFilter::make('report')
					->label(__('messages.report'))
					->attribute('report'),

				SelectFilter::make('company_advisor_id')
					->label(__('messages.company_advisor_model'))
					->options(
						array_unique(
							CompanyAdvisor::all()
								->pluck('firstname', 'id')
								->toArray()
						)
					)->searchable(),

				SelectFilter::make('activity_id')
					->label(__('messages.activity'))
					->options(
						array_unique(
							EducationCategory::all()
								->pluck('name', 'id')
								->toArray()
						)
					)->searchable(),
			])
			->actions([
				Tables\Actions\ViewAction::make(),
				EditAction::make(),
				Tables\Actions\DeleteAction::make()
			])
			->bulkActions([
				Tables\Actions\DeleteBulkAction::make(),
				ExportBulkAction::make()->exports([
					ExcelExport::make()->withColumns([

						Column::make('id')
							->heading('ID'),

						Column::make('matriculation_number')
							->heading(__('messages.matriculation_number')),

						Column::make('section')
							->getStateUsing(fn($record): string => self::getSections()[$record->section])
							->heading(__('messages.section')),

						Column::make('salutation')
							->getStateUsing(fn($record): string => $record->salutation == 1 ? 'JA' : 'NEIN')
							->heading(__('messages.salutation')),

						Column::make('title')
							->heading(__('messages.title')),

						Column::make('firstname')
							->heading(__('messages.firstname')),

						Column::make('lastname')
							->heading(__('messages.lastname')),

						Column::make('street')
							->heading(__('messages.street')),

						Column::make('street_number')
							->heading(__('messages.street_number')),

						Column::make('stairs')
							->heading(__('messages.stairs')),

						Column::make('door')
							->heading(__('messages.door')),

						Column::make('city')
							->heading(__('messages.city')),

						Column::make('postcode')
							->heading(__('messages.postcode')),

						Column::make('email')
							->heading(__('messages.email')),

						Column::make('phone_number')
							->heading(__('messages.phone_number')),

						Column::make('iban')
							->heading(__('messages.iban')),

						Column::make('bic')
							->heading(__('messages.bic')),

						Column::make('svnr')
							->heading(__('messages.svnr')),

						Column::make('birthdate')
							->heading(__('messages.birthdate')),

						Column::make('report')
							->getStateUsing(fn($record): string => $record->report == 1 ? 'JA' : 'NEIN')
							->heading(__('messages.report')),

						Column::make('user.name')
							->heading(__('messages.updated_from')),

						Column::make('pva.name')
							->heading("PVA Name"),

						Column::make('pva.firstname')
							->heading("PVA Vorname"),

						Column::make('pva.lastname')
							->heading("PVA Nachname"),

						Column::make('pva.email')
							->heading("PVA Email"),

						Column::make('pva.phone_number')
							->heading("PVA Telefonnummer"),

						Column::make('activity.name')
							->heading(__('messages.activity')),

						Column::make('ams_advisor.firstname')
							->heading("AMS Berater Vorname"),

						Column::make('ams_advisor.lastname')
							->heading("AMS Berater Nachname"),

						Column::make('ams_advisor.email')
							->heading("AMS Berater Email"),

						Column::make('ams_advisor.phone_number')
							->heading("AMS Berater Telefonnummer"),

						Column::make('ams_status')
							->getStateUsing(fn($record): string => $record->ams_status == 1 ? 'JA' : 'NEIN')
							->heading(__('messages.ams_status')),

						Column::make('career_goal')
							->heading(__('messages.career_goal')),

						Column::make('last_activity')
							->heading(__('messages.last_activity')),

						Column::make('pre_qualification')
							->heading(__('messages.pre_qualification')),

						Column::make('internship_location')
							->heading(__('messages.internship_location')),

						Column::make('weekly_hours')
							->heading(__('messages.weekly_hours')),

						Column::make('entitlement_to_care_leave')
							->heading(__('messages.entitlement_to_care_leave')),

						Column::make('coaching_date')
							->heading(__('messages.coaching_date')),

						Column::make('education_plan')
							->getStateUsing(fn($record): string => $record->education_plan == 1 ? 'JA' : 'NEIN')
							->heading(__('messages.education_plan')),

						Column::make('education_plan_approved')
							->getStateUsing(fn($record): string => $record->education_plan_approved == 1 ? 'JA' : 'NEIN')
							->heading(__('messages.education_plan_approved')),

						Column::make('training_agreement')
							->getStateUsing(fn($record): string => $record->training_agreement == 1 ? 'JA' : 'NEIN')
							->heading(__('messages.training_agreement')),

						Column::make('entry_notification_land')
							->getStateUsing(fn($record): string => $record->entry_notification_land == 1 ? 'JA' : 'NEIN')
							->heading(__('messages.entry_notification_land')),

						Column::make('schALG_conversion')
							->getStateUsing(fn($record): string => $record->schALG_conversion == 1 ? 'JA' : 'NEIN')
							->heading(__('messages.schALG_conversion')),

						Column::make('agreement_with_company')
							->getStateUsing(fn($record): string => $record->agreement_with_company == 1 ? 'JA' : 'NEIN')
							->heading(__('messages.agreement_with_company')),

						Column::make('agreement_date')
							->heading(__('messages.agreement_date')),

						Column::make('land_advance')
							->getStateUsing(fn($record): string => $record->land_advance == 1 ? 'JA' : 'NEIN')
							->heading(__('messages.land_advance')),

						Column::make('land_final_bill')
							->getStateUsing(fn($record): string => $record->land_final_bill == 1 ? 'JA' : 'NEIN')
							->heading(__('messages.land_final_bill')),

						Column::make('share_sign_land')
							->heading(__('messages.share_sign_land')),

						Column::make('education_cost_plan')
							->heading(__('messages.education_cost_plan')),

						Column::make('subsidy_coursecost_charged')
							->getStateUsing(fn($record): string => $record->subsidy_coursecost_charged == 1 ? 'JA' : 'NEIN')
							->heading(__('messages.subsidy_coursecost_charged')),

						Column::make('subsidy_coursecost_amount')
							->heading(__('messages.subsidy_coursecost_amount')),

						Column::make('land_request_ub')
							->heading(__('messages.land_request_ub')),

						Column::make('land_request_educationcosts')
							->heading(__('messages.land_request_educationcosts')),

						Column::make('land_request_date')
							->heading(__('messages.land_request_date')),

						Column::make('land_request_approval_date')
							->heading(__('messages.land_request_approval_date')),

						Column::make('land_request_zlg_date')
							->heading(__('messages.land_request_zlg_date')),

						Column::make('land_final_bill_amount')
							->heading(__('messages.land_final_bill_amount')),

						Column::make('land_final_bill_approval_date')
							->heading(__('messages.land_final_bill_approval_date')),

						Column::make('land_final_bill_zlg_date')
							->heading(__('messages.land_final_bill_zlg_date')),

					])
						->withWriterType(\Maatwebsite\Excel\Excel::XLSX)
						->withFilename(self::getPluralModelLabel() . "_" . date("Y-m-d"))
						->askForFilename()
						->askForWriterType()
				])

			]);
	}

	public static function infolist(Infolist $infolist): Infolist
	{
		return $infolist
			->schema([
				Components\Section::make('Allgemeine Daten')
					->schema([
						Components\Split::make([
							Components\Section::make()
								->label('Personenbezogene Daten')
								->schema([
									Components\TextEntry::make('title')
										->label(__('messages.title')),
									Components\TextEntry::make('lastname')
										->label(__('messages.lastname')),
									Components\TextEntry::make('firstname')
										->label(__('messages.firstname')),
									Components\TextEntry::make('birthdate')
										->label(__('messages.birthdate')),
									Components\TextEntry::make('svnr')
										->label(__('messages.svnr'))
								])->columns(2)
								->heading("Personenbezogene Daten"),

							Components\Section::make()
								->label('Kontaktinformationen')
								->schema([
									Components\TextEntry::make('email')
										->label(__('messages.email'))
										->url(fn($record) => "mailto:{$record->email}")
										->limit(16),
									Components\TextEntry::make('phone_number')
										->label(__('messages.phone_number'))
										->url(fn($record) => "tel:{$record->phone_number}"),
								])->columns(2)
								->heading("Kontaktinformationen"),

							Components\Section::make()
								->label('Adresse')
								->schema([
									Components\TextEntry::make('street')
										->label(__('messages.street')),
									Components\TextEntry::make('street_number')
										->label(__('messages.street_number')),
									Components\TextEntry::make('city')
										->label(__('messages.city')),
									Components\TextEntry::make('postcode')
										->label(__('messages.postcode')),
									Components\TextEntry::make('door')
										->label(__('messages.door')),
									Components\TextEntry::make('stairs')
										->label(__('messages.stairs')),
								])->columns(2)
								->heading("Adresse"),

						])->columns(3)
					])->collapsible(true),

				Components\Section::make('Karrieredaten')
					->schema([
						Components\Split::make([
							Components\Section::make()
								->label('Karrieredaten')
								->schema([
									Components\TextEntry::make('career_goal')
										->getStateUsing(fn($record): string => (empty($record->career_goal)) ? '/' : $record->career_goal)
										->label(__('messages.career_goal')),

									Components\TextEntry::make('pre_qualification')
										->getStateUsing(fn($record): string => (empty($record->pre_qualification)) ? '/' : $record->pre_qualification)
										->label(__('messages.pre_qualification')),

									Components\TextEntry::make('internship_location')
										->getStateUsing(fn($record): string => (empty($record->internship_location)) ? '/' : $record->internship_location)
										->label(__('messages.internship_location')),

									Components\TextEntry::make('vacation_entitlement')
										->getStateUsing(fn($record): string => (empty($record->vacation_entitlement)) ? '/' : $record->vacation_entitlement)
										->label(__('messages.vacation_entitlement')),

									Components\TextEntry::make('weekly_hours')
										->getStateUsing(fn($record): string => (empty($record->weekly_hours)) ? '/' : $record->weekly_hours)
										->label(__('messages.weekly_hours')),

									Components\TextEntry::make('entitlement_to_care_leave')
										->getStateUsing(fn($record): string => (empty($record->entitlement_to_care_leave)) ? '/' : $record->entitlement_to_care_leave)
										->label(__('messages.entitlement_to_care_leave')),

									Components\TextEntry::make('education_plan')
										->label(__('messages.education_plan'))
										->badge()
										->getStateUsing(fn($record): string => !empty($record->education_plan) ? ($record->education_plan == '1' ? 'JA' : 'NEIN') : ('/')),

									Components\TextEntry::make('education_plan_approved')
										->label(__('messages.education_plan_approved'))
										->badge()
										->getStateUsing(fn($record): string => !empty($record->education_plan_approved) ? ($record->education_plan_approved == '1' ? 'JA' : 'NEIN') : ('/')),

									Components\TextEntry::make('training_agreement')
										->label(__('messages.training_agreement'))
										->badge()
										->getStateUsing(fn($record): string => !empty($record->training_agreement) ? ($record->training_agreement == '1' ? 'JA' : 'NEIN') : ('/')),

									Components\TextEntry::make('entry_notification_land')
										->label(__('messages.entry_notification_land'))
										->badge()
										->getStateUsing(fn($record): string => !empty($record->entry_notification_land) ? ($record->entry_notification_land == '1' ? 'JA' : 'NEIN') : ('/')),

									Components\TextEntry::make('schALG_conversion')
										->label(__('messages.schALG_conversion'))
										->badge()
										->getStateUsing(fn($record): string => !empty($record->schALG_conversion) ? ($record->schALG_conversion == '1' ? 'JA' : 'NEIN') : ('/')),

									Components\TextEntry::make('agreement_with_company')
										->label(__('messages.agreement_with_company'))
										->badge()
										->getStateUsing(fn($record): string => !empty($record->agreement_with_company) ? ($record->agreement_with_company == '1' ? 'JA' : 'NEIN') : ('/')),

									Components\TextEntry::make('land_advance')
										->label(__('messages.land_advance'))
										->badge()
										->getStateUsing(fn($record): string => !empty($record->land_advance) ? ($record->land_advance == '1' ? 'JA' : 'NEIN') : ('/')),

									Components\TextEntry::make('land_final_bill')
										->label(__('messages.land_final_bill'))
										->badge()
										->getStateUsing(!empty($record->land_final_bill) ? ($record->land_final_bill == '1' ? 'JA' : 'NEIN') : ('/')),

									Components\TextEntry::make('share_sign_land')
										->getStateUsing(fn($record): string => (empty($record->share_sign_land)) ? '/' : $record->share_sign_land)
										->label(__('messages.share_sign_land')),

									Components\TextEntry::make('education_cost_plan')
										->getStateUsing(fn($record): string => (empty($record->education_cost_plan)) ? '/' : $record->education_cost_plan)
										->label(__('messages.education_cost_plan')),

									Components\TextEntry::make('subsidy_coursecost_charged')
										->label(__('messages.subsidy_coursecost_charged'))
										->badge()
										->getStateUsing(!empty($record->subsidy_coursecost_charged) ? ($record->subsidy_coursecost_charged == 1 ? 'JA' : 'NEIN') : ('/')),

									Components\TextEntry::make('subsidy_coursecost_amount')
										->label(__('messages.subsidy_coursecost_amount'))
										->getStateUsing(fn($record): string => (empty($record->subsidy_coursecost_amount)) ? '/' : $record->subsidy_coursecost_amount),

									Components\TextEntry::make('land_request_ub')
										->label(__('messages.land_request_ub'))
										->getStateUsing(fn($record): string => (empty($record->land_request_ub)) ? '/' : $record->land_request_ub),

									Components\TextEntry::make('land_request_qb')
										->label(__('messages.land_request_qb'))
										->getStateUsing(fn($record): string => (empty($record->land_request_qb)) ? '/' : $record->land_request_qb),

									Components\TextEntry::make('land_request_educationcosts')
										->label(__('messages.land_request_educationcosts'))
										->getStateUsing(fn($record): string => (empty($record->land_request_educationcosts)) ? '/' : $record->land_request_educationcosts),

									Components\TextEntry::make('land_final_bill_amount')
										->label(__('messages.land_final_bill_amount'))
										->getStateUsing(fn($record): string => (empty($record->land_final_bill_amount)) ? '/' : $record->land_final_bill_amount),

								])->columns(3)
						])
					])->collapsible(true)
					->collapsed(),

				Components\Section::make('Bankdaten')
					->schema([
						Components\Split::make([
							Components\Section::make()
								->label('Bankdaten')
								->schema([
									Components\TextEntry::make('iban')
										->label(__('messages.iban')),
									Components\TextEntry::make('bic')
										->label(__('messages.bic'))
								])->columns(2)
						])
					])->collapsible(true)
					->collapsed(),

				Components\Section::make(__('messages.ams_advisor_model'))
					->schema([
						Components\Split::make([
							Components\Section::make()
								->label('Personenbezogene Daten')
								->schema([
									Components\TextEntry::make('ams_advisor.title')
										->label(__('messages.title')),
									Components\TextEntry::make('ams_advisor.lastname')
										->label(__('messages.lastname')),
									Components\TextEntry::make('ams_advisor.firstname')
										->label(__('messages.firstname')),
									Components\TextEntry::make('ams_advisor.department_head')
										->label(__('messages.department_head'))
										->getStateUsing(fn($record): string => !empty($record->ams_advisor->department_head) ? ($record->ams_advisor->department_head == 1 ? 'JA' : 'NEIN') : ('/')),
								])->columns(2)
								->heading("Personenbezogene Daten"),

							Components\Section::make()
								->label('Kontaktinformationen')
								->schema([
									Components\TextEntry::make('ams_advisor.email')
										->label(__('messages.email'))
										->url(fn($record) => !empty($record->ams_advisor->email) ? "mailto:{$record->ams_advisor->email}" : "/")
										->limit(16),

									Components\TextEntry::make('ams_advisor.phone_number')
										->label(__('messages.phone_number'))
										->url(fn($record) => !empty($record->ams_advisor->phone_number) ? "tel:{$record->ams_advisor->phone_number}" : "/"),

								])->columns(2)
								->heading("Kontaktinformationen"),

							Components\Section::make()
								->label(__('messages.ams_rgs_model'))
								->schema([
									Components\TextEntry::make('ams_advisor.ams_rgs.name')
										->label(__('messages.name')),

									Components\TextEntry::make('ams_advisor.ams_rgs.street')
										->label(__('messages.street')),

									Components\TextEntry::make('ams_advisor.ams_rgs.postcode')
										->label(__('messages.postcode')),

									Components\TextEntry::make('ams_advisor.ams_rgs.city')
										->label(__('messages.city')),

									Components\TextEntry::make('ams_advisor.ams_rgs.email')
										->label(__('messages.email'))
										->url(fn($record) => !empty($record->ams_advisor->ams_rgs->email) ? "mailto:{$record->ams_advisor->ams_rgs->email}" : "/"),

									Components\TextEntry::make('ams_advisor.ams_rgs.phone_number')
										->label(__('messages.phone_number'))
										->url(!empty($record->ams_advisor->ams_rgs->phone_number) ? "tel:{$record->ams_advisor->ams_rgs->phone_number}" : "/"),

								])->columns(2)
								->heading(__('messages.ams_rgs_model')),
						])->columns(3)
					])->collapsible(true)
					->collapsed(),

				Components\Section::make(__('messages.company_advisor_model'))
					->schema([
						Components\Split::make([
							Components\Section::make()
								->label('Personenbezogene Daten')
								->schema([
									Components\TextEntry::make('company_advisor.title')
										->label(__('messages.title')),
									Components\TextEntry::make('company_advisor.lastname')
										->label(__('messages.lastname')),
									Components\TextEntry::make('company_advisor.firstname')
										->label(__('messages.firstname')),
									Components\TextEntry::make('company_advisor.department_head')
										->label(__('messages.department_head'))
										->getStateUsing(fn($record): string => !empty($record->company_advisor->department_head) ? ($record->company_advisor->department_head == 1 ? 'JA' : 'NEIN') : ('/')),
								])->columns(2)
								->heading("Personenbezogene Daten"),

							Components\Section::make()
								->label('Kontaktinformationen')
								->schema([
									Components\TextEntry::make('company_advisor.email')
										->label(__('messages.email'))
										->url(fn($record) => !empty($record->company_advisor->email) ? "mailto:{$record->company_advisor->email}" : "/")
										->limit(16),

									Components\TextEntry::make('company_advisor.phone_number')
										->label(__('messages.phone_number'))
										->url(fn($record) => (!empty($record->company_advisor->phone_number) ? "tel:{$record->company_advisor->phone_number}" : "/")),

								])->columns(2)
								->heading("Kontaktinformationen"),


							Components\Section::make()
								->label(__('messages.company_model'))
								->schema([
									Components\TextEntry::make('company_advisor.company.companyname1')
										->label(__('messages.companyname1')),

									Components\TextEntry::make('company_advisor.company.companyname2')
										->label(__('messages.companyname2')),

									Components\TextEntry::make('company_advisor.company.postcode')
										->label(__('messages.postcode')),

									Components\TextEntry::make('company_advisor.company.city')
										->label(__('messages.city')),

									Components\TextEntry::make('company_advisor.company.street')
										->label(__('messages.street')),

									Components\TextEntry::make('company_advisor.company.email')
										->label(__('messages.email'))
										->url(fn($record) => !empty($record->company_advisor->company->email) ? "mailto:{$record->company_advisor->company->email}" : "/")
										->limit(16),

									Components\TextEntry::make('company_advisor.company.phone_number')
										->label(__('messages.phone_number'))
										->url(fn($record) => !empty($record->company_advisor->company->phone_number) ? "tel:{$record->company_advisor->company->phone_number}" : "/"),

									Components\TextEntry::make('company_advisor.company.fax')
										->label(__('messages.fax')),

									Components\TextEntry::make('company_advisor.company.cooperation_agreement')
										->getStateUsing(fn($record): ?string => !empty($record->company_advisor->company->cooperation_agreement) ? ($record->company_advisor->company->cooperation_agreement == 1 ? 'JA' : 'NEIN') : ('/'))
										->label(__('messages.cooperation_agreement')),

									Components\TextEntry::make('company_advisor.company.website')
										->limit(16)
										->url('company_advisor.company.website')
										->label(__('messages.website')),

									Components\TextEntry::make('company_advisor.company.phone_number_mobil')
										->url('company_advisor.company.phone_number_mobil')
										->label(__('messages.phone_number_mobil')),

								])->columns(2)
								->heading(__('messages.company_model')),
						])->columns(3)
					])->collapsible(true)
					->collapsed(),

				Components\Section::make('Informationen')
					->schema([
						Components\Split::make([
							Components\Section::make()
								->label('Bankdaten')
								->schema([

									Components\TextEntry::make('user.name')
										->label(__('messages.updated_from')),

									Components\TextEntry::make('updated_at')
										->label(__('messages.updated_at')),
									Components\TextEntry::make('created_at')
										->label(__('messages.created_at'))
								])->columns(2)
						])
					])->collapsible(true)
					->collapsed(),

			]);
	}

	public static function getRelations(): array
	{
		return [
			//	RelationManagers\AmsAdvisorsRelationManager::class,
			//	RelationManagers\CompanyAdvisorsRelationManager::class,
			RelationManagers\NotesRelationManager::class,
			RelationManagers\DocumentsRelationManager::class,
			RelationManagers\ContributionsRelationManager::class,
			RelationManagers\AbsencesRelationManager::class
		];
	}

	public static function getNavigationBadge(): ?string
	{
		return static::$model::count();
	}

	public static function getWidgets(): array
	{
		return [
			ParticipantOverview::class,
		];
	}

	public static function getPages(): array
	{
		return [
			'index' => Pages\ListParticipants::route('/'),
			'create' => Pages\CreateParticipant::route('/create'),
			'edit' => Pages\EditParticipant::route('/{record}/edit'),
			'view' => Pages\ViewParticipant::route('/{record}')
		];
	}
}
