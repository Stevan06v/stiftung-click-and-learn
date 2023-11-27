<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArchiveResource\Pages;
use App\Filament\Resources\ArchiveResource\RelationManagers;
use App\Models\Archive;
use App\Models\Participant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ArchiveResource extends Resource
{
	protected static ?string $model = Participant::class;

	public static function getModelLabel(): string
	{
		return __('messages.archive');
	}

	public static function getPluralModelLabel(): string
	{
		return __('messages.archive');
	}

	protected static ?string $navigationIcon = 'fas-box-archive';
	protected static ?string $navigationGroup = 'ARCHIV';

	protected static ?int $navigationSort = 999;


	public static function form(Form $form): Form
	{
		return $form
			->schema([
				//
			]);
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
				TextColumn::make('phone_number')
					->sortable()
					->searchable()
					->placeholder('none')
					->label(__('messages.phone_number'))
					->url(fn($record) => "tel:{$record->phone_number}")
					->icon('fas-phone-volume'),
				TextColumn::make('email')
					->sortable()
					->searchable()
					->placeholder('none')
					->label(__('messages.email'))
					->url(fn($record) => "mailto:{$record->email}")
					->icon('fas-envelope')
			])
			->filters([
				Tables\Filters\TrashedFilter::make()
				->default(0)
			])
			->actions([
				Tables\Actions\ForceDeleteAction::make(),
				Tables\Actions\RestoreAction::make(),
			])
			->bulkActions([
				Tables\Actions\BulkActionGroup::make([
					Tables\Actions\ForceDeleteBulkAction::make(),
					Tables\Actions\RestoreBulkAction::make(),
				]),
			])
			->emptyStateActions([
			]);
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
			'index' => Pages\ListArchives::route('/'),
			'create' => Pages\CreateArchive::route('/create'),
		];
	}

	public static function getNavigationBadge(): ?string
	{
		return static::$model::whereNotNull('deleted_at')->withTrashed()->count();
	}

	public static function getEloquentQuery(): Builder
	{
		return parent::getEloquentQuery()
			->withoutGlobalScopes([
				SoftDeletingScope::class,
			]);
	}

}
