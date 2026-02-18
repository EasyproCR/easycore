<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\FormsComponent;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Collection;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    // protected static ?string $navigationLabel = 'Employees';

    protected static ?string $navigationLabel = null;
    protected static ?string $navigationGroup = null;

    public static function getLabel(): ?string
    {
        return __('resources.user.label');
    }

    public static function getPluralLabel(): ?string
    {
        return __('resources.user.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.user.navigation');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('resources.user.navigation_group');
    }

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?int $navigationSort = 4;
    // protected static ?string $modelLabel = 'usuario';
    // protected static ?string $pluralModelLabel = 'Usuarios';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('resources.user.userSection'))
                    ->columns(2)
                    ->schema([
                        // ...
                        Forms\Components\TextInput::make('name')
                            ->label(__('translate.user.name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label(__('translate.user.email'))
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('password')
                            ->label(__('translate.user.password'))
                            ->password()
                            ->hiddenOn([
                                'edit',
                                'view'
                            ])
                            ->revealable()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DateTimePicker::make('email_verified_at')
                            ->label(__('translate.user.email_verified_at')),
                        Forms\Components\Select::make('roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->required(),
                        Forms\Components\Toggle::make('state')
                            ->label(__('translate.user.state'))
                            ->required(),
                        Forms\Components\Select::make('country_id')
                            ->label('País')
                            ->relationship('country', 'name')
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->with(['roles']))
            ->columns([
                ImageColumn::make('avatar_url')
                    ->label(__('translate.user.avatar_url'))
                    ->circular()
                    ->alignCenter()
                    ->height(50)
                    ->width(50),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('translate.user.name'))
                    ->searchable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('translate.user.email'))
                    ->searchable()
                    ->alignCenter(),
                Tables\Columns\IconColumn::make('state')
                    ->label(__('translate.user.state'))
                    ->boolean()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('country.name')
                    ->label('País')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'super_admin' => 'info',
                        'panel_user' => 'warning',
                        'soporte' => 'success',
                        'rrhh' => 'danger',
                        'ventas' => 'success',
                        'gerente' => 'info',
                        'servicio_al_cliente' => 'danger',
                        'contabilidad' => 'success',
                        'referido' => 'warning',
                        'gerente_sucursal' => 'info',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->label(__('translate.user.email_verified_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('translate.user.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('translate.user.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
