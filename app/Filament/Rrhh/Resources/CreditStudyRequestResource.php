<?php

namespace App\Filament\Rrhh\Resources;

use App\Filament\Rrhh\Resources\CreditStudyRequestResource\Pages;
use App\Filament\Rrhh\Resources\CreditStudyRequestResource\RelationManagers;
use App\Models\CreditStudyRequest;
use App\Models\PersonalCustomer;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Parallax\FilamentComments\Tables\Actions\CommentsAction;

class CreditStudyRequestResource extends Resource
{
    protected static ?string $model = CreditStudyRequest::class;

    protected static ?string $navigationLabel = null;
    protected static ?string $navigationGroup = null;

    public static function getLabel(): ?string
    {
        return __('resources.credit_request.label');
    }

    public static function getPluralLabel(): ?string
    {
        return __('resources.credit_request.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.credit_request.navigation');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('resources.employee.navigation_group');
    }

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('resources.credit_request.customer_section'))
                    ->columns(2)
                    ->schema([
                        Select::make('user_id')
                            ->label(__('translate.credit_request.user_id'))
                            ->relationship(name: 'user', titleAttribute:'name', modifyQueryUsing: fn (Builder $query) => $query->where('state', true))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('personal_customer_id', null);
                            }),
                        Select::make('personal_customer_id')
                            ->label(__('translate.credit_request.personal_customer_id'))
                            ->options(fn (Get $get): Collection => PersonalCustomer::query()
                                ->where('user_id', $get('user_id'))
                                ->pluck('full_name', 'id')
                            )
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $customer = PersonalCustomer::find($state);

                                if ($customer) {
                                    $set('national_id_display', $customer->national_id);
                                    $set('phone_number_display', $customer->phone_number);
                                    $set('email_display', $customer->email);
                                } else {
                                    $set('national_id_display', null);
                                    $set('phone_number_display', null);
                                    $set('email_display', null);
                                }
                            })
                            ->searchable()
                            ->preload(),

                        TextInput::make('national_id_display')
                            ->label(__('translate.credit_request.national_id'))
                            ->visible(fn (Get $get): bool => $get('personal_customer_id') !== null)
                            ->disabled()
                            ->dehydrated(false)
                            ->hiddenOn('edit'),

                        TextInput::make('phone_number_display')
                            ->label(__('translate.credit_request.phone_number'))
                            ->visible(fn (Get $get): bool => $get('personal_customer_id') !== null)
                            ->disabled()
                            ->dehydrated(false)
                            ->hiddenOn('edit'),

                        TextInput::make('email_display')
                            ->label(__('translate.credit_request.email'))
                            ->visible(fn (Get $get): bool => $get('personal_customer_id') !== null)
                            ->disabled()
                            ->dehydrated(false)
                            ->hiddenOn('edit'),
                    ]),

                Section::make(__('resources.credit_request.request_section'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('property')
                            ->label(__('translate.credit_request.property'))
                            ->required(),
                        Textarea::make('request_reason')
                            ->label(__('translate.credit_request.request_reason')),
                    ]),

                Section::make(__('resources.credit_request.sales_section'))
                    ->columns(2)
                    ->schema([
                        Select::make('request_status')
                            ->label(__('translate.credit_request.request_status'))
                            ->options(__('translate.credit_request.options_request_status'))
                            ->reactive()
                            ->required(),
                        Textarea::make('sales_comments')
                            ->label(__('translate.credit_request.sales_comments')),
                        Textarea::make('rejection_reason')
                            ->label(__('translate.credit_request.rejection_reason'))
                            ->visible(fn (Get $get): bool => $get('request_status') == 'rejected'),
                        FileUpload::make('documents')
                            ->label(__('translate.credit_request.documents'))
                            ->multiple()
                            ->visible(fn (Get $get): bool => $get('request_status') == 'approved')
                            ->downloadable()
                            ->disk('azure_public')
                            ->visibility('public')
                            ->directory('credit_study_requests/' .now()->format('Y-m-d'))
                            ->maxFiles(5),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('translate.credit_request.user_id'))
                    ->searchable()
                    ->alignLeft(),
                TextColumn::make('personal_customer.full_name')
                    ->label(__('translate.credit_request.personal_customer_id'))
                    ->searchable()
                    ->alignLeft(),
                TextColumn::make('personal_customer.national_id')
                    ->label(__('translate.credit_request.national_id'))
                    ->searchable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('personal_customer.phone_number')
                    ->label(__('translate.credit_request.phone_number'))
                    ->searchable()
                    ->alignRight()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('personal_customer.email')
                    ->label(__('translate.credit_request.email'))
                    ->searchable()
                    ->alignLeft()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('property')
                    ->label(__('translate.credit_request.property'))
                    ->alignRight()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('request_status')
                    ->label(__('translate.credit_request.request_status'))
                    ->alignCenter()
                    ->badge()
                    ->formatStateUsing(fn ($state) => __('translate.credit_request.options_request_status.' . $state))
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                    }),
                TextColumn::make('sales_comments')
                    ->label(__('translate.credit_request.sales_comments'))
                    ->alignLeft()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('rejection_reason')
                    ->label(__('translate.credit_request.rejection_reason'))
                    ->alignLeft()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('translate.credit_request.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('updated_at')
                    ->label(__('translate.credit_request.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
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
            'index' => Pages\ListCreditStudyRequests::route('/'),
            'create' => Pages\CreateCreditStudyRequest::route('/create'),
            'edit' => Pages\EditCreditStudyRequest::route('/{record}/edit'),
        ];
    }
}
