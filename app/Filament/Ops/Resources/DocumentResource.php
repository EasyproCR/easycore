<?php

namespace App\Filament\Ops\Resources;

use App\Filament\Ops\Resources\DocumentResource\Pages;
use App\Filament\Ops\Resources\DocumentResource\RelationManagers;
use App\Models\Document;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static ?int $navigationSort = 6;

    protected static ?string $navigationLabel = null;
    protected static ?string $navigationGroup = null;

    public static function getLabel(): ?string
    {
        return __('resources.document.label');
    }

    public static function getPluralLabel(): ?string
    {
        return __('resources.document.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.document.navigation');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('resources.operation.navigation_group');
    }

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('resources.document.sectionDocument'))
                ->columns(3)
                ->schema([
                    TextInput::make('name')
                        ->label(__('translate.document.name'))
                        ->maxLength(255)
                        ->required(),
                    Textarea::make('description')
                        ->label(__('translate.document.description')),
                    FileUpload::make('image')
                        ->directory('images/' . now()->format('Y/m/d'))
                        ->label(__('translate.document.image'))
                        ->image()
                        ->disk('azure_public')
                        ->visibility('public')
                        ->downloadable(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('translate.document.name'))
                    ->searchable()
                    ->alignCenter(),
                TextColumn::make('description')
                    ->label(__('translate.document.description'))
                    ->alignCenter(),
                ImageColumn::make('image')
                    ->disk('azure_public')
                    ->label(__('translate.document.image'))
                    ->alignCenter(),
            ])
            ->filters([
                //
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
            'index' => Pages\ListDocuments::route('/'),
            'create' => Pages\CreateDocument::route('/create'),
            'edit' => Pages\EditDocument::route('/{record}/edit'),
        ];
    }
}
