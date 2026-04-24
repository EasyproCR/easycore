<?php

namespace App\Filament\Gerente\Resources;

use App\Filament\Gerente\Resources\PersonalCustomerResource\Pages;
use App\Filament\Gerente\Resources\PersonalCustomerResource\RelationManagers;
use App\Models\PersonalCustomer;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Parallax\FilamentComments\Tables\Actions\CommentsAction;

class PersonalCustomerResource extends Resource
{
    protected static ?string $model = PersonalCustomer::class;

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = null;
    protected static ?string $navigationGroup = null;

    public static function getLabel(): ?string
    {
        return __('resources.personal_customer.label');
    }

    public static function getPluralLabel(): ?string
    {
        return __('resources.personal_customer.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.personal_customer.navigation');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('resources.employee.navigation_group');
    }

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('resources.personal_customer.section_customer'))
                    ->columns(3)
                    ->schema([
                        Select::make('user_id')
                            ->label(__('translate.customer.user_id'))
                            ->relationship(
                                name: 'user',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query) => $query->where('state', true),
                            )
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('full_name')
                            ->label(__('translate.customer.full_name'))
                            ->required()
                            ->maxLength(255),
                        TextInput::make('national_id')
                            ->label(__('translate.customer.national_id'))
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label(__('translate.customer.email'))
                            ->email()
                            ->maxLength(255),
                        TextInput::make('phone_number')
                            ->label(__('translate.customer.phone_number'))
                            ->maxLength(255)
                            ->required(),
                        Textarea::make('address')
                            ->label(__('translate.customer.address')),
                        Select::make('contact_preferences')
                            ->label(__('translate.customer.contact_preferences'))
                            ->options([
                                'email' => __('translate.customer.options_cpreferences.0'),
                                'whatsapp' => __('translate.customer.options_cpreferences.1'),
                                'phone' => __('translate.customer.options_cpreferences.2'),
                                'other' => __('translate.customer.options_cpreferences.3')
                            ]),
                        Select::make('customer_type')
                            ->label(__('translate.customer.customer_type'))
                            ->options([
                                'buyer' => __('translate.customer.options_cust_type.0'),
                                'seller' => __('translate.customer.options_cust_type.1'),
                                'investor' => __('translate.customer.options_cust_type.2'),
                                'tenant' => __('translate.customer.options_cust_type.3'),
                                'other' => __('translate.customer.options_cust_type.4'),
                            ]),
                        DatePicker::make('date_of_birth')
                            ->label(__('translate.personal_customer.date_of_birth')),
                        Textarea::make('customer_need')
                            ->label(__('translate.personal_customer.customer_need'))
                            ->columnSpanFull()
                            ->required(),
                    ]),

                    Section::make(__('resources.personal_customer.section_follow_up'))
                    ->columns(3)
                    ->schema([
                        Select::make('prospect_status')
                            ->label(__('translate.personal_customer.prospect_status'))
                            ->options([
                                'hot' => __('translate.personal_customer.options_prospect_status.0'),
                                'warm' => __('translate.personal_customer.options_prospect_status.1'),
                                'cold' => __('translate.personal_customer.options_prospect_status.2'),
                            ]),
                        Select::make('next_action')
                            ->label(__('translate.personal_customer.next_action'))
                            ->options([
                                'call' => __('translate.personal_customer.options_next_action.0'),
                                'send_info' => __('translate.personal_customer.options_next_action.1'),
                                'schedule_call' => __('translate.personal_customer.options_next_action.2'),
                            ]),
                        DatePicker::make('next_action_date')
                            ->label(__('translate.personal_customer.next_action_date')),
                    ]),

                    Section::make(__('resources.personal_customer.section_financial'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('budget_usd')
                            ->label(__('translate.customer.budget_usd')),
                        TextInput::make('budget_crc')
                            ->label(__('translate.customer.budget_crc')),
                        Toggle::make('financing')
                            ->label(__('translate.customer.financing')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('translate.customer.user_id'))
                    ->searchable()
                    ->alignCenter(),
                TextColumn::make('prospect_status')
                    ->label(__('translate.personal_customer.prospect_status'))
                    ->alignCenter()
                    ->badge()
                    ->formatStateUsing(function ($state){
                        return match ($state) {
                            'hot' => __('translate.personal_customer.options_prospect_status.0'),
                            'warm' => __('translate.personal_customer.options_prospect_status.1'),
                            'cold' => __('translate.personal_customer.options_prospect_status.2'),
                        };
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'hot' => 'success',
                        'warm' => 'warning',
                        'cold' => 'danger',
                    }),
                TextColumn::make('full_name')
                    ->label(__('translate.customer.full_name'))
                    ->searchable()
                    ->alignCenter(),
                TextColumn::make('national_id')
                    ->label(__('translate.customer.national_id'))
                    ->searchable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('email')
                    ->label(__('translate.customer.email'))
                    ->searchable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('phone_number')
                    ->label(__('translate.customer.phone_number'))
                    ->searchable()
                    ->alignCenter(),
                TextColumn::make('address')
                    ->label(__('translate.customer.address'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('contact_preferences')
                    ->label(__('translate.customer.contact_preferences'))
                    ->alignCenter()
                    ->formatStateUsing(function ($state){
                        return match ($state) {
                            'email' => __('translate.customer.options_cpreferences.0'),
                            'whatsapp' => __('translate.customer.options_cpreferences.1'),
                            'phone' => __('translate.customer.options_cpreferences.2'),
                            'other' => __('translate.customer.options_cpreferences.3')
                        };
                    })
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('date_of_birth')
                    ->label(__('translate.personal_customer.date_of_birth'))
                    ->date()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('customer_type')
                    ->label(__('translate.customer.customer_type'))
                    ->alignCenter()
                    ->formatStateUsing(function ($state){
                        return match ($state) {
                            'buyer' => __('translate.customer.options_cust_type.0'),
                            'seller' => __('translate.customer.options_cust_type.1'),
                            'investor' => __('translate.customer.options_cust_type.2'),
                            'tenant' => __('translate.customer.options_cust_type.3'),
                            'other' => __('translate.customer.options_cust_type.4')
                        };
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('customer_need')
                    ->label(__('translate.personal_customer.customer_need'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('next_action')
                    ->label(__('translate.personal_customer.next_action'))
                    ->alignCenter()
                    ->formatStateUsing(function ($state){
                        return match ($state) {
                            'call' => __('translate.personal_customer.options_next_action.0'),
                            'send_info' => __('translate.personal_customer.options_next_action.1'),
                            'schedule_call' => __('translate.personal_customer.options_next_action.2'),
                        };
                    })
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('next_action_date')
                    ->label(__('translate.personal_customer.next_action_date'))
                    ->date()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('budget_usd')
                    ->label(__('translate.customer.budget_usd'))
                    ->alignCenter()
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('budget_crc')
                    ->label(__('translate.customer.budget_crc'))
                    ->alignCenter()
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: false),
                IconColumn::make('financing')
                    ->label(__('translate.customer.financing'))
                    ->alignCenter()
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label(__('translate.customer.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('updated_at')
                    ->label(__('translate.customer.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('prospect_status')
                    ->label(__('translate.personal_customer.prospect_status'))
                    ->options([
                        'hot' => __('translate.personal_customer.options_prospect_status.0'),
                        'warm' => __('translate.personal_customer.options_prospect_status.1'),
                        'cold' => __('translate.personal_customer.options_prospect_status.2'),
                    ]),
                SelectFilter::make('contact_preferences')
                    ->label(__('translate.customer.contact_preferences'))
                    ->options([
                        'email' => __('translate.customer.options_cpreferences.0'),
                        'whatsapp' => __('translate.customer.options_cpreferences.1'),
                        'phone' => __('translate.customer.options_cpreferences.2'),
                        'other' => __('translate.customer.options_cpreferences.3')
                    ]),
                SelectFilter::make('next_action')
                    ->label(__('translate.personal_customer.next_action'))
                    ->options([
                        'call' => __('translate.personal_customer.options_next_action.0'),
                        'send_info' => __('translate.personal_customer.options_next_action.1'),
                        'schedule_call' => __('translate.personal_customer.options_next_action.2'),
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
            'index' => Pages\ListPersonalCustomers::route('/'),
            'create' => Pages\CreatePersonalCustomer::route('/create'),
            'edit' => Pages\EditPersonalCustomer::route('/{record}/edit'),
        ];
    }
}
