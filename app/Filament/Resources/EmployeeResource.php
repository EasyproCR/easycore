<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\City;
use App\Models\Employee;
use App\Models\State;
use Filament\Actions\DeleteAction;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Parallax\FilamentComments\Tables\Actions\CommentsAction;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationLabel = null;
    protected static ?string $navigationGroup = null;

    public static function getLabel(): ?string
    {
        return __('resources.employee.label');
    }

    public static function getPluralLabel(): ?string
    {
        return __('resources.employee.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.employee.navigation');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('resources.employee.navigation_group');
    }

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('resources.employee.sectionEmployee'))
                    ->columns(2)
                    ->schema([
                        Select::make('user_id')
                            ->relationship(name: 'user', titleAttribute: 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('name')
                            ->label(__('translate.employee.name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label(__('translate.employee.email'))
                            ->email()
                            ->maxLength(255),
                        Forms\Components\Select::make('job_position')
                            ->label(__('translate.employee.job_position'))
                            ->options([
                                'administrative' => __('translate.employee.options_job_position.0'),
                                'sales_manager' => __('translate.employee.options_job_position.1'),
                                'sales_advisor' => __('translate.employee.options_job_position.2'),
                            ]),
                        Forms\Components\Select::make('progress_status')
                            ->label(__('translate.employee.progress_status'))
                            ->options([
                                'pending' => __('translate.employee.options_progress_status.0'),
                                'in_form' => __('translate.employee.options_progress_status.1'),
                                'certified' => __('translate.employee.options_progress_status.2'),
                                'retired' => __('translate.employee.options_progress_status.3'),
                                'referred' => __('translate.employee.options_progress_status.4'),
                            ])
                            ->required(),
                        Forms\Components\Toggle::make('contract_status')
                            ->label(__('translate.employee.contract_status'))
                            ->required(),
                    ]),

                Section::make(__('resources.employee.sectionPersonal'))
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('personal_email')
                            ->label(__('translate.employee.personal_email'))
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('national_id')
                            ->label(__('translate.employee.national_id'))
                            ->maxLength(15),
                        Forms\Components\Select::make('marital_status')
                            ->label(__('translate.employee.marital_status'))
                            ->options([
                                'single' => __('translate.employee.options_marital_status.0'),
                                'married' => __('translate.employee.options_marital_status.1'),
                                'divorced' => __('translate.employee.options_marital_status.2'),
                                'widowed' => __('translate.employee.options_marital_status.3'),
                                'free_union' => __('translate.employee.options_marital_status.4'),
                            ]),
                        Forms\Components\TextInput::make('profession')
                            ->label(__('translate.employee.profession')),
                        Forms\Components\TextInput::make('phone_number')
                            ->label(__('translate.employee.phone_number'))
                            ->maxLength(15),
                        Forms\Components\TextInput::make('license_plate')
                            ->label(__('translate.employee.license_plate')),
                        Forms\Components\DatePicker::make('birthday')
                            ->label(__('translate.employee.birthday')),
                        Forms\Components\FileUpload::make('credid')
                            ->acceptedFileTypes(['application/pdf'])
                            ->directory('attachments/' . now()->format('Y/m/d'))
                            ->downloadable(),
                        Forms\Components\FileUpload::make('contract')
                            ->label('Contrato')
                            ->disk('azure_public')
                            ->visibility('public')
                            ->acceptedFileTypes(['application/pdf'])
                            ->directory('attachments/contract/' . now()->format('Y/m/d'))
                            ->downloadable(),
                    ]),

                Section::make(__('resources.employee.sectionAddress'))
                    ->columns(3)
                    ->schema([
                        // ...
                        Forms\Components\Select::make('country_id')
                            ->label(__('translate.employee.country'))
                            ->relationship(name: 'country', titleAttribute: 'name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('state_id', null);
                                $set('city_id', null);
                            }),
                        Forms\Components\Select::make('state_id')
                            ->label(__('translate.employee.locationState'))
                            ->options(
                                fn(Get $get): Collection => State::query()
                                    ->where('country_id', $get('country_id'))
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('city_id', null);
                            }),
                        Forms\Components\Select::make('city_id')
                            ->label(__('translate.employee.locationCity'))
                            ->options(
                                fn(Get $get): Collection => City::query()
                                    ->where('state_id', $get('state_id'))
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload(),
                        Forms\Components\Textarea::make('address')
                            ->label(__('translate.employee.address'))
                            ->columnSpanFull(),
                    ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->with(['user', 'country', 'locationState', 'locationCity']))
            ->columns([
                TextColumn::make('user.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('translate.employee.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('translate.employee.email'))
                    ->searchable(),
                Tables\Columns\IconColumn::make('contract_status')
                    ->label(__('translate.employee.contract_status'))
                    ->boolean()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('progress_status')
                    ->label(__('translate.employee.progress_status'))
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'pending' => __('translate.employee.options_progress_status.0'),
                            'in_form' => __('translate.employee.options_progress_status.1'),
                            'certified' => __('translate.employee.options_progress_status.2'),
                            'retired' => __('translate.employee.options_progress_status.3'),
                            'referred' => __('translate.employee.options_progress_status.4'),
                        };
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'in_form' => 'info',
                        'certified' => 'success',
                        'retired' => 'danger',
                        'referred' => 'info',
                    })
                    ->searchable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('job_position')
                    ->label(__('translate.employee.job_position'))
                    ->searchable()
                    ->alignCenter()
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'administrative' => __('translate.employee.options_job_position.0'),
                            'sales_manager' => __('translate.employee.options_job_position.1'),
                            'sales_advisor' => __('translate.employee.options_job_position.2'),
                        };
                    })
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('national_id')
                    ->label(__('translate.employee.national_id'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('phone_number')
                    ->label(__('translate.employee.phone_number'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('personal_email')
                    ->label(__('translate.employee.personal_email'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('profession')
                    ->label(__('translate.employee.profession'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('license_plate')
                    ->label(__('translate.employee.license_plate'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('country.name')
                    ->label(__('translate.employee.country'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('locationState.name')
                    ->label(__('translate.employee.locationState'))
                    ->alignCenter()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('locationCity.name')
                    ->label(__('translate.employee.locationCity'))
                    ->alignCenter()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('address')
                    ->label(__('translate.employee.address'))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('birthday')
                    ->label(__('translate.employee.birthday'))
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('marital_status')
                    ->label(__('translate.employee.marital_status'))
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('translate.employee.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('translate.employee.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make(__('translate.employee.job_position'))
                    ->options([
                        'administrative' => __('translate.employee.options_job_position.0'),
                        'sales_manager' => __('translate.employee.options_job_position.1'),
                        'sales_advisor' => __('translate.employee.options_job_position.2'),
                    ]),
            ])
            ->actions([
                ActionGroup::make([
                    CommentsAction::make()
                        ->color('info'),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()
                        ->color('warning'),
                    Tables\Actions\DeleteAction::make()
                        ->color('danger'),
                ])
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
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
