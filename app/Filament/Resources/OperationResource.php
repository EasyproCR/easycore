<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OperationResource\Pages;
use App\Filament\Resources\OperationResource\RelationManagers;
use App\Models\Operation;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OperationResource extends Resource
{
    protected static ?string $model = Operation::class;

    protected static ?string $navigationLabel = null;
    protected static ?string $navigationGroup = null;

    public static function getLabel(): ?string
    {
        return __('resources.operation.label');
    }

    public static function getPluralLabel(): ?string
    {
        return __('resources.operation.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.operation.navigation');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('resources.operation.navigation_group');
    }

    protected static ?string $navigationIcon = 'heroicon-o-beaker';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('resources.operation.sectionOperation'))
                ->columns(3)
                ->schema([
                    Select::make('user_id')
                        ->label(__('translate.operation.user_id'))
                        ->relationship(
                            name: 'user',
                            titleAttribute: 'name',
                            modifyQueryUsing: fn (Builder $query) => $query->where('state', true),
                        ),
                    Select::make('document_id')
                        ->label(__('translate.operation.document_id'))
                        ->relationship(
                            name: 'document',
                            titleAttribute: 'name',
                        ),
                    Select::make('departament_id')
                        ->label(__('translate.operation.departament_id'))
                        ->relationship(
                            name: 'departament',
                            titleAttribute: 'name',
                        ),
                    RichEditor::make('scope')
                        ->label(__('translate.operation.scope'))
                        ->columnSpanFull(),
                    RichEditor::make('benefits')
                        ->label(__('translate.operation.benefits'))
                        ->columnSpanFull(),
                    RichEditor::make('references')
                        ->label(__('translate.operation.references'))
                        ->columnSpanFull(),
                    RichEditor::make('policies')
                        ->label(__('translate.operation.policies'))
                        ->columnSpanFull(),
                    RichEditor::make('steps')
                        ->label(__('translate.operation.steps'))
                        ->columnSpanFull(),
                    FileUpload::make('attachments')
                        ->directory('attachments/' . now()->format('Y/m/d'))
                        ->disk('azure_public')
                        ->visibility('public')
                        ->label(__('translate.operation.attachments'))
                        ->columnSpanFull()
                        ->downloadable(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('translate.operation.user_id'))
                    ->alignCenter()
                    ->searchable(),
                TextColumn::make('document.name')
                    ->label(__('translate.operation.document_id'))
                    ->alignCenter()
                    ->searchable(),
                TextColumn::make('departament.name')
                    ->label(__('translate.operation.departament_id'))
                    ->alignCenter()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(__('translate.operation.created_at'))
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('translate.operation.updated_at'))
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->label(__('translate.operation.user_id'))
                    ->relationship(
                        name: 'user',
                        titleAttribute: 'name',
                    ),
                SelectFilter::make('document_id')
                    ->label(__('translate.operation.document_id'))
                    ->relationship(
                        name: 'document',
                        titleAttribute: 'name',
                    ),
                SelectFilter::make('departament_id')
                    ->label(__('translate.operation.departament_id'))
                    ->relationship(
                        name: 'departament',
                        titleAttribute: 'name',
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListOperations::route('/'),
            'create' => Pages\CreateOperation::route('/create'),
            'edit' => Pages\EditOperation::route('/{record}/edit'),
        ];
    }
}
