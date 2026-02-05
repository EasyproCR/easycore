<?php

namespace App\Filament\Sales\Resources;

use App\Filament\Sales\Resources\ThirdPartyPropertyResource\Pages;
use App\Filament\Sales\Resources\ThirdPartyPropertyResource\RelationManagers;
use App\Models\{Country, State, City, ThirdPartyProperty, User, PersonalCustomer};
use Filament\Forms\Form;
use Filament\Forms\Components\{Section, TextInput, Select, DatePicker, Textarea, FileUpload};
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\{SelectFilter, TrashedFilter};
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Parallax\FilamentComments\Tables\Actions\CommentsAction;

class ThirdPartyPropertyResource extends Resource
{
    protected static ?string $model = ThirdPartyProperty::class;

    protected static ?string $navigationLabel = null;
    protected static ?string $navigationGroup = null;

    public static function getLabel(): ?string
    {
        return __('resources.third_party_property.label');
    }

    public static function getPluralLabel(): ?string
    {
        return __('resources.third_party_property.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.third_party_property.navigation');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('resources.employee.navigation_group');
    }

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información de la propiedad')
                    ->columns(12)
                    ->schema([
                        TextInput::make('name')->label('Nombre de la propiedad')->required()->maxLength(150)->columnSpan(6),

                        Select::make('status')->label('Estado de la propiedad')->options([
                            'pending' => 'Pendiente',
                            'available' => 'Disponible',
                            'unavailable' => 'No disponible',
                            'rejected' => 'Rechazada',
                        ])->default('pending')->required()->columnSpan(3),

                        TextInput::make('finca_number')->label('Número de finca')->unique(ignoreRecord: true)->required()->maxLength(150)->columnSpan(3),

                        Select::make('country_id')->label('País')->options(
                            fn() => Country::query()
                                ->orderBy('name')
                                ->pluck('name', 'id')
                        )->searchable()->preload()->required()->reactive()->afterStateUpdated(fn(Set $set) => $set('state_id', null))->columnSpan(4),

                        Select::make('state_id')->label('Provincia')->options(function (Get $get) {
                            $country = $get('country_id');

                            if (! $country) {
                                return [];
                            }

                            return State::query()
                                ->where('country_id', $country)
                                ->orderBy('name')
                                ->pluck('name', 'id');
                        })->searchable()->required()->reactive()->afterStateUpdated(fn(Set $set) => $set('city_id', null))->columnSpan(4),

                        Select::make('city_id')->label('Cantón')->options(function (Get $get) {
                            $state = $get('state_id');

                            if (! $state) {
                                return [];
                            }

                            return City::query()
                                ->where('state_id', $state)
                                ->orderBy('name')
                                ->pluck('name', 'id');
                        })->searchable()->required()->columnSpan(4),

                        Textarea::make('address')->label('Dirección')->rows(2)->required()->columnSpan(12),

                        DatePicker::make('received_at')->label('Fecha de ingreso')->required()->native(false)->columnSpan(3),

                        Select::make('service_type')->label('Tipo de servicio')->options([
                            'alquiler' => 'Alquiler',
                            'venta' => 'Venta',
                        ])->required()->reactive()->columnSpan(3),

                        Select::make('currency_code')->label('Moneda')->options([
                            'CRC' => 'CRC',
                            'USD' => 'USD',
                        ])->required()->reactive()->columnSpan(3),

                        TextInput::make('monthly_amount')->label('Monto mensual (CRC)')->numeric()->prefix('₡')->visible(function (Get $get) {
                            return $get('service_type') === 'alquiler' && $get('currency_code') === 'CRC';
                        })->required(function (Get $get) {
                            return $get('service_type') === 'alquiler' && $get('currency_code') === 'CRC';
                        })->dehydrated(function (Get $get) {
                            return $get('service_type') === 'alquiler' && $get('currency_code') === 'CRC';
                        })->afterStateUpdated(
                            function ($state, Set $set, Get $get) {
                                if ($get('service_type') === 'alquiler' && $get('currency_code') === 'CRC') {
                                    $set('sale_amount', null);
                                }
                            }
                        )->columnSpan(3),

                        TextInput::make('monthly_amount_usd')->label('Monto mensual (USD)')->numeric()->prefix('$')->visible(function (Get $get) {
                            return $get('service_type') === 'alquiler' && $get('currency_code') === 'USD';
                        })->required(function (Get $get) {
                            return $get('service_type') === 'alquiler' && $get('currency_code') === 'USD';
                        })->dehydrated(function (Get $get) {
                            return $get('service_type') === 'alquiler' && $get('currency_code') === 'USD';
                        })->afterStateUpdated(
                            function ($state, Set $set, Get $get) {
                                if ($get('service_type') === 'alquiler' && $get('currency_code') === 'USD') {
                                    $set('sale_amount_usd', null);
                                }
                            }
                        )->columnSpan(3),

                        TextInput::make('sale_amount')->label('Monto de venta (CRC)')->numeric()->prefix('₡')->visible(function (Get $get) {
                            return $get('service_type') === 'venta' && $get('currency_code') === 'CRC';
                        })->required(function (Get $get) {
                            return $get('service_type') === 'venta' && $get('currency_code') === 'CRC';
                        })->dehydrated(function (Get $get) {
                            return $get('service_type') === 'venta' && $get('currency_code') === 'CRC';
                        })->afterStateUpdated(
                            function ($state, Set $set, Get $get) {
                                if ($get('service_type') === 'venta' && $get('currency_code') === 'CRC') {
                                    $set('monthly_amount', null);
                                }
                            }
                        )->columnSpan(3),

                        TextInput::make('sale_amount_usd')->label('Monto de venta (USD)')->numeric()->prefix('$')->visible(function (Get $get) {
                            return $get('service_type') === 'venta' && $get('currency_code') === 'USD';
                        })->required(function (Get $get) {
                            return $get('service_type') === 'venta' && $get('currency_code') === 'USD';
                        })->dehydrated(function (Get $get) {
                            return $get('service_type') === 'venta' && $get('currency_code') === 'USD';
                        })->afterStateUpdated(
                            function ($state, Set $set, Get $get) {
                                if ($get('service_type') === 'venta' && $get('currency_code') === 'USD') {
                                    $set('monthly_amount_usd', null);
                                }
                            }
                        )->columnSpan(3),

                        Textarea::make('details')->label('Detalles y caracteristicas')->rows(3)->columnSpan(12),
                    ]),

                Section::make('Responsables y contactos')
                    ->columns(12)
                    ->schema([
                        Select::make('user_id')->label('Asesor responsable')->options(
                            fn() =>
                            User::query()
                                ->where('state', true)
                                ->orderBy('name')
                                ->pluck('name', 'id')
                        )->searchable()->preload()->required()->reactive()->afterStateUpdated(
                            fn(Set $set) => $set('personal_customer_id', null)
                        )->columnSpan(4),

                        Select::make('personal_customer_id')->label('Propietario')->options(
                            fn(Get $get) =>
                            PersonalCustomer::query()
                                ->where('user_id', $get('user_id'))
                                ->orderBy('id', 'desc')
                                ->limit(500)
                                ->pluck('full_name', 'id')
                        )->searchable()->preload()->columnSpan(4),

                        Select::make('supervisor_id')->label('Supervisor de ventas')->options(
                            function (Get $get) {
                                return User::query()
                                    ->where('state', true)
                                    ->when($get('user_id'), function ($query) use ($get) {
                                        $query->where('id', '!=', $get('user_id'));
                                    })
                                    ->orderBy('name')
                                    ->pluck('name', 'id');
                            }
                        )->searchable()->preload()->columnSpan(4),
                    ]),

                Section::make('Adjuntos')
                    ->columns(12)
                    ->schema([
                        FileUpload::make('contract_path')->label('Contrato (PDF/JPEG/PNG)')->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])->directory('tdp/contracts/' . now()->format('Y/m/d')) ->disk('azure_public')
                            ->visibility('public') ->preserveFilenames()->downloadable()->required()->columnSpan(6),

                        FileUpload::make('registry_study_path')->label('Estudio de registro (PDF/JPEG/PNG)')->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png']) ->disk('azure_public')
                            ->visibility('public') ->directory('tdp/registry/' . now()->format('Y/m/d'))->preserveFilenames()->downloadable()->required()->columnSpan(6),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')->label('Propiedad')->searchable()->sortable(),
                TextColumn::make('status')->label('Estado')->formatStateUsing(function ($state) {
                    return match ($state) {
                        'pending' => 'Pendiente',
                        'available' => 'Disponible',
                        'unavailable' => 'No disponible',
                        'rejected' => 'Rechazada',
                    };
                })->badge()->color(fn(string $state): string => match ($state) {
                    'pending' => 'warning',
                    'available' => 'success',
                    'unavailable' => 'info',
                    'rejected' => 'danger',
                })->alignCenter(),
                TextColumn::make('service_type')->label('Tipo de servicio')->formatStateUsing(function ($state) {
                    return match ($state) {
                        'alquiler' => 'Alquiler',
                        'venta' => 'Venta',
                    };
                })->badge()->color(fn(string $state): string => match ($state) {
                    'alquiler' => 'success',
                    'venta' => 'info',
                })->alignCenter(),
                TextColumn::make('currency_code')->label('Moneda')->formatStateUsing(function ($state) {
                    return match ($state) {
                        'CRC' => 'CRC',
                        'USD' => 'USD',
                    };
                })->badge()->color(fn(string $state): string => match ($state) {
                    'CRC' => 'warning',
                    'USD' => 'danger',
                })->alignCenter(),
                TextColumn::make('monthly_amount')->label('Mensual ₡')->money('CRC', true)->toggleable(),
                TextColumn::make('monthly_amount_usd')->label('Mensual $')->money('USD', true)->toggleable(),
                TextColumn::make('sale_amount')->label('Venta ₡')->money('CRC', true)->toggleable(),
                TextColumn::make('sale_amount_usd')->label('Venta $')->money('USD', true)->toggleable(),
                TextColumn::make('finca_number')->label('Finca')->searchable()->toggleable(),
                TextColumn::make('city.name')->label('Cantón')->searchable()->toggleable(),
                TextColumn::make('state.name')->label('Provincia')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('country.name')->label('País')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('received_at')->label('Ingreso')->date()->toggleable(),
                TextColumn::make('user.name')->label('Asesor')->searchable()->toggleable(),
                TextColumn::make('supervisor.name')->label('Supervisor')->searchable()->toggleable(),
                TextColumn::make('owner.full_name')->label('Propietario')->searchable()->toggleable(),
                TextColumn::make('created_at')->label('Creado')->dateTime()->since()->toggleable(),
                TextColumn::make('updated_at')->label('Actualizado')->dateTime()->since()->toggleable(),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('service_type')->label('Tipo de servicio')->options([
                    'alquiler' => 'Alquiler',
                    'venta' => 'Venta',
                ]),
                SelectFilter::make('country_id')->label('País')
                    ->options(fn() => Country::orderBy('name')->pluck('name', 'id')->all()),
                SelectFilter::make('state_id')->label('Provincia')
                    ->options(fn() => State::orderBy('name')->pluck('name', 'id')->all()),
                SelectFilter::make('city_id')->label('Cantón')
                    ->options(fn() => City::orderBy('name')->pluck('name', 'id')->all()),
            ])
            ->actions([
                ActionGroup::make([
                    CommentsAction::make()
                        ->color('info'),
                    Tables\Actions\EditAction::make()
                        ->mutateFormDataUsing(function (array $data): array {
                            if (($data['service_type'] ?? null) === 'alquiler') {
                                $data['sale_amount'] = null;
                            } elseif (($data['service_type'] ?? null) === 'venta') {
                                $data['monthly_amount'] = null;
                            }
                            return $data;
                        })->color('warning'),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
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
            'index' => Pages\ListThirdPartyProperties::route('/'),
            'create' => Pages\CreateThirdPartyProperty::route('/create'),
            'edit' => Pages\EditThirdPartyProperty::route('/{record}/edit'),
        ];
    }
}
