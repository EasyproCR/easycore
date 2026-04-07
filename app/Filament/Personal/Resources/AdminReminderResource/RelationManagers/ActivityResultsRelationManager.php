<?php

namespace App\Filament\Personal\Resources\AdminReminderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ActivityResultsRelationManager extends RelationManager
{
    protected static string $relationship = 'activityResults';
    protected static ?string $title = 'Resultados de la Actividad';
    protected static ?string $modelLabel = 'resultado';
    protected static ?string $pluralModelLabel = 'resultados';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('revision_date')
                    ->label('Fecha de la Revisión')
                    ->required()
                    ->native(false)
                    ->default(now()),

                Forms\Components\Select::make('result')
                    ->label('Resultado')
                    ->options([
                        'Satisfactorio' => 'Satisfactorio',
                        'Negativo'      => 'Negativo',
                        'N/A'           => 'N/A',
                    ])
                    ->required(),

                Forms\Components\Textarea::make('detail')
                    ->label('Detalle')
                    ->columnSpanFull()
                    ->rows(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('result')
            ->columns([
                Tables\Columns\TextColumn::make('revision_date')
                    ->label('Fecha de la Revisión')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('result')
                    ->label('Resultado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Satisfactorio' => 'success',
                        'Negativo'      => 'danger',
                        'N/A'           => 'gray',
                        default         => 'gray',
                    })
                    ->searchable(),

                Tables\Columns\TextColumn::make('detail')
                    ->label('Detalle')
                    ->limit(50)
                    ->searchable(),
            ])
            ->filters([
                //
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
            ]);
    }
}
