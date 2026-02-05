<?php

namespace App\Filament\Personal\Resources;

use App\Filament\Personal\Resources\ProjectResource\Pages;
use App\Filament\Personal\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Parallax\FilamentComments\Tables\Actions\CommentsAction;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationLabel = null;
    protected static ?string $navigationGroup = null;

    public static function getLabel(): ?string
    {
        return __('resources.project.label');
    }

    public static function getPluralLabel(): ?string
    {
        return __('resources.project.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.project.navigation');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('resources.customer.navigation_group');
    }

    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::user()->id);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('resources.project.sectionProject'))
                    ->columns(3)
                    ->schema([
                        TextInput::make('name')
                            ->label(__('translate.project.name'))
                            ->required()
                            ->maxLength(255),
                        TextInput::make('progress')
                            ->label(__('translate.project.progress'))
                            ->required()
                            ->maxLength(255)
                            ->suffix('%'),
                        Select::make('project_status')
                            ->label(__('translate.project.project_status'))
                            ->options(__('translate.project.options_project_status'))
                            ->required(),
                        Select::make('priority')
                            ->label(__('translate.project.priority'))
                            ->options(__('translate.project.options_priority'))
                            ->required(),
                        Textarea::make('expected_benefit')
                            ->label(__('translate.project.expected_benefit')),
                        DatePicker::make('request_date')
                            ->label(__('translate.project.request_date')),
                        DatePicker::make('last_updated_at')
                            ->label(__('translate.project.last_updated_at')),
                        Textarea::make('observations')
                            ->label(__('translate.project.observations')),
                        FileUpload::make('attachments')
                            ->label(__('translate.project.attachments'))
                            ->multiple()
                            ->downloadable()
                            ->disk('azure_public')
                            ->visibility('public')
                            ->directory('proyectos/' .now()->format('Y/m/d'))
                            ->maxFiles(5),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('translate.project.name'))
                    ->searchable(),
                TextColumn::make('progress')
                    ->label(__('translate.project.progress'))
                    ->alignCenter()
                    ->formatStateUsing(fn($state) => $state . '%'),
                TextColumn::make('project_status')
                    ->label(__('translate.project.project_status'))
                    ->alignCenter()
                    ->badge()
                    ->formatStateUsing(
                        fn($state) => __('translate.project.options_project_status.' . $state)
                    )
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'in_progress' => 'info',
                        'finished' => 'success',
                        'stopped' => 'danger',
                    }),
                TextColumn::make('priority')
                    ->label(__('translate.project.priority'))
                    ->alignCenter()
                    ->badge()
                    ->formatStateUsing(
                        fn($state) => __('translate.project.options_priority.' . $state)
                    )
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'warning',
                        'medium' => 'info',
                        'high' => 'danger',
                    }),
                TextColumn::make('expected_benefit')
                    ->label(__('translate.project.expected_benefit'))
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('request_date')
                    ->label(__('translate.project.request_date'))
                    ->date()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('last_updated_at')
                    ->label(__('translate.project.last_updated_at'))
                    ->date()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('observations')
                    ->label(__('translate.project.observations'))
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('created_at')
                    ->label(__('translate.project.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('updated_at')
                    ->label(__('translate.project.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('priority')
                    ->label(__('translate.project.priority'))
                    ->options(__('translate.project.options_priority')),
            ])
            ->actions([
                ActionGroup::make([
                    CommentsAction::make()
                        ->color('info'),
                    Tables\Actions\EditAction::make()
                        ->color('warning'),
                    Tables\Actions\DeleteAction::make(),
                ])
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
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
