<?php

namespace App\Filament\Personal\Resources;

use App\Enums\ReminderFrequency;
use App\Enums\ReminderType;
use App\Filament\Personal\Resources\AdminReminderResource\Pages;
use App\Filament\Personal\Resources\AdminReminderResource\RelationManagers;
use App\Models\AdminReminder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AdminReminderResource extends Resource
{
    protected static ?string $model = AdminReminder::class;
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $modelLabel = 'recordatorio';
    protected static ?string $pluralModelLabel = 'mis recordatorios';
    protected static ?string $navigationGroup = null;

    public static function getNavigationGroup(): ?string
    {
        return __('resources.operation.navigation_group');
    }

    /**
     * Restrict records to the currently authenticated user.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth::id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Configuración')
                    ->schema([
                        Forms\Components\Placeholder::make('user_name')
                            ->content(fn (?AdminReminder $record) => $record?->user?->name ?? '—')
                            ->label('Responsable'),

                        Forms\Components\Placeholder::make('frequency')
                            ->content(fn (?AdminReminder $record) => $record?->frequency?->getLabel() ?? '—')
                            ->label('Frecuencia'),

                        Forms\Components\Placeholder::make('type')
                            ->content(fn (?AdminReminder $record) => $record?->type?->getLabel() ?? '—')
                            ->label('Tipo'),

                        Forms\Components\Placeholder::make('content')
                            ->content(fn (?AdminReminder $record) => $record?->content ?? '—')
                            ->label('Contenido')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Programación')
                    ->schema([
                        Forms\Components\Placeholder::make('starts_at')
                            ->content(fn (?AdminReminder $record) => $record?->starts_at?->format('Y-m-d') ?? '—')
                            ->label('Inicio'),

                        Forms\Components\Placeholder::make('ends_at')
                            ->content(fn (?AdminReminder $record) => $record?->ends_at?->format('Y-m-d') ?? 'Sin fecha de fin')
                            ->label('Fin'),

                        Forms\Components\Placeholder::make('is_active')
                            ->content(fn (?AdminReminder $record) => $record?->is_active ? 'Sí' : 'No')
                            ->label('Activo'),
                    ])->columns(2),

                Forms\Components\Section::make('Historial')
                    ->schema([
                        Forms\Components\Placeholder::make('next_run_at')
                            ->content(fn (?AdminReminder $record) => $record?->next_run_at?->format('Y-m-d H:i:s') ?? 'Pendiente')
                            ->label('Próximo recordatorio'),

                        Forms\Components\Placeholder::make('last_sent_at')
                            ->content(fn (?AdminReminder $record) => $record?->last_sent_at?->format('Y-m-d H:i:s') ?? 'Nunca')
                            ->label('Último recordatorio'),

                        Forms\Components\Placeholder::make('failure_count')
                            ->content(fn (?AdminReminder $record) => $record?->failure_count ?? 0)
                            ->label('Intentos fallidos'),
                    ])->columns(2)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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
                    ->label('Próximo recordatorio'),

                Tables\Columns\TextColumn::make('last_sent_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Último recordatorio'),

                Tables\Columns\TextColumn::make('failure_count')
                    ->numeric()
                    ->sortable()
                    ->color(fn (int $state) => $state > 0 ? 'danger' : 'success')
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
                    ->query(fn (Builder $query) => $query->where('is_active', true))
                    ->label('Activo'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Ver / Registrar')
                    ->icon('heroicon-o-eye'),
            ])
            ->bulkActions([])
            ->defaultSort('next_run_at', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ActivityResultsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAdminReminders::route('/'),
            'edit'   => Pages\EditAdminReminder::route('/{record}/edit'),
        ];
    }
}
