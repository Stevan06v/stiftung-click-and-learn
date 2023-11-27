<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{

	protected static ?string $model = User::class;

	public static function getModelLabel(): string
	{
		return __('messages.users');
	}

	public static function getPluralModelLabel(): string
	{
		return __('messages.users');
	}

	protected static ?string $navigationIcon = 'far-circle-user';
	protected static ?string $navigationGroup = 'BENUTZER';

	protected static ?int $navigationSort = 998;


	public static function form(Form $form): Form
	{
		return $form
			->schema([
				Section::make('Ändere deine Zugangsdaten')
					->schema([
					TextInput::make('name')
						->name('name')
						->type('text')
						->required()
						->placeholder('Name')
						->maxLength(255),

					TextInput::make('email')
						->name('email')
						->type('email')
						->email()
						->required()
						->placeholder('Email')
						->maxLength(255),

					# password requirement
					TextInput::make('password')
						->required(fn(Page $livewire): bool => $livewire instanceof CreateRecord)
						->name('password')
						->label('Password')
						->type('password')
						->password()
						->required()
						->placeholder('Password')
						->maxLength(255)
						->same('passwordConfirmation')
						->dehydrateStateUsing(fn($state) => Hash::make($state)), # hash the password

					TextInput::make('passwordConfirmation')
						->name('passwordConfirmation')
						->label('Passwort ändern')
						->required(fn(Page $livewire): bool => $livewire instanceof CreateRecord)
						->dehydrated(false)
						->placeholder('Bestätige dein Passwort')
						->password()
				])
			]);
	}

	public static function table(Table $table): Table
	{
		return $table
			->columns([
				TextColumn::make('name')
					->name('name'),
				TextColumn::make('email')
					->name('email')
					->url(fn($record) => "mailto:{$record->email}"),
				TextColumn::make('password')
					->name('password')
					->limit(10)
					->getStateUsing(fn ($record):string => "*********")
					->copyable(false)

			])
			->filters([
				//
			])
			->actions([
				Tables\Actions\EditAction::make(),
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
			'index' => Pages\ListUsers::route('/'),
			'create' => Pages\CreateUser::route('/create'),
			'edit' => Pages\EditUser::route('/{record}/edit'),
		];
	}
}
