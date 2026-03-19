<?php

namespace App\Filament\Ops\Resources;

use App\Enums\ReminderFrequency;
use App\Enums\ReminderType;
use App\Filament\Ops\Resources\AdminReminderResource\Pages;
use App\Models\AdminReminder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AdminReminderResource extends Resource
{
    protected static ?string $model = AdminReminder::class;
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $modelLabel = 'recordatorio administrativo';
    protected static ?string $pluralModelLabel = 'recordatorios administrativos';
    protected static ?string $navigationGroup = null;

    public static function getNavigationGroup(): ?string
    {
        return __('resources.operation.navigation_group');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Configuración')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required()
                            ->label('Responsable'),

                        Forms\Components\Select::make('frequency')
                            ->options(ReminderFrequency::class)
                            ->required()
                            ->enum(ReminderFrequency::class)
                            ->label('Frecuencia'),

                        Forms\Components\Select::make('type')
                            ->options(ReminderType::class)
                            ->required()
                            ->enum(ReminderType::class)
                            ->label('Tipo'),

                        Forms\Components\Textarea::make('content')
                            ->required()
                            ->columnSpanFull()
                            ->rows(3)
                            ->label('Contenido'),
                    ])->columns(2),

                Forms\Components\Section::make('Programación')
                    ->schema([
                        Forms\Components\DatePicker::make('starts_at')
                            ->required()
                            ->default(now()->addDay()->startOfDay()->addHours(8))
                            ->label('Inicio (Primer ejecución)')
                            ->native(false),

                        Forms\Components\DatePicker::make('ends_at')
                            ->label('Fin (Opcional)')
                            ->native(false),

                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->label('Activo'),
                    ])->columns(2),

                Forms\Components\Section::make('Historial')
                    ->schema([
                        Forms\Components\Placeholder::make('next_run_at')
                            ->content(fn(?AdminReminder $record) => $record?->next_run_at?->format('Y-m-d H:i:s') ?? 'Pendiente')
                            ->label('Proximo recordatorio'),

                        Forms\Components\Placeholder::make('last_sent_at')
                            ->content(fn(?AdminReminder $record) => $record?->last_sent_at?->format('Y-m-d H:i:s') ?? 'Nunca')
                            ->label('Ultimo recordatorio'),

                        Forms\Components\Placeholder::make('failure_count')
                            ->content(fn(?AdminReminder $record) => $record?->failure_count ?? 0)
                            ->label('Intentos fallidos'),

                        Forms\Components\Placeholder::make('last_error_message')
                            ->content(fn(?AdminReminder $record) => $record?->last_error_message ?? 'None')
                            ->visible(fn(?AdminReminder $record) => !empty($record?->last_error_message))
                            ->label('Último error'),
                    ])->columns(2)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable()
                    ->label('Responsable'),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->sortable()
                    ->label('Tipo'),

                Tables\Columns\TextColumn::make('frequency')
                    ->sortable()
                    ->label('Frecuencia'),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Activo'),

                Tables\Columns\TextColumn::make('next_run_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Proximo recordatorio'),

                Tables\Columns\TextColumn::make('last_sent_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Ultimo recordatorio'),

                Tables\Columns\TextColumn::make('failure_count')
                    ->numeric()
                    ->sortable()
                    ->color(fn(int $state) => $state > 0 ? 'danger' : 'success')
                    ->label('Intentos fallidos'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('frequency')
                    ->options(ReminderFrequency::class)
                    ->label('Frecuencia'),

                Tables\Filters\SelectFilter::make('type')
                    ->options(ReminderType::class)
                    ->label('Tipo'),

                Tables\Filters\Filter::make('active')
                    ->query(fn(Builder $query) => $query->where('is_active', true))
                    ->label('Activo'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('next_run_at', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\AdminReminderResource\RelationManagers\ActivityResultsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdminReminders::route('/'),
            'create' => Pages\CreateAdminReminder::route('/create'),
            'edit' => Pages\EditAdminReminder::route('/{record}/edit'),
        ];
    }
}
