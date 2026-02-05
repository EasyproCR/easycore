<?php

namespace App\Filament\Rrhh\Resources;

use App\Filament\Rrhh\Resources\AdRequestResource\Pages;
use App\Filament\Rrhh\Resources\AdRequestResource\RelationManagers;
use App\Models\AdRequest;
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
use Parallax\FilamentComments\Tables\Actions\CommentsAction;

class AdRequestResource extends Resource
{
    protected static ?string $model = AdRequest::class;

    protected static ?string $navigationLabel = null;
    protected static ?string $navigationGroup = null;

    public static function getLabel(): ?string
    {
        return __('resources.ad_request.label');
    }

    public static function getPluralLabel(): ?string
    {
        return __('resources.ad_request.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.ad_request.navigation');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('resources.campaign.navigation_group');
    }

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('resources.ad_request.section_request'))
                    ->columns(3)
                    ->description(__('resources.ad_request.section_description'))
                    ->schema([
                        Select::make('user_id')
                            ->label(__('translate.ad_request.user_id'))
                            ->relationship(
                                name: 'user',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn(Builder $query) => $query->where('state', true),
                            )
                            ->preload()
                            ->searchable()
                            ->required(),
                        Select::make('platform')
                            ->label(__('translate.ad_request.platform'))
                            ->options(__('translate.ad_request.options_platform'))
                            ->required(),
                        TextInput::make('ad_url')
                            ->label(__('translate.ad_request.ad_url'))
                            ->url()
                            ->required(),
                        TextInput::make('target_age')
                            ->label(__('translate.ad_request.target_age')),
                        TextInput::make('target_location')
                            ->label(__('translate.ad_request.target_location')),
                        DatePicker::make('start_date')
                            ->label(__('translate.ad_request.start_date'))
                            ->required(),
                        DatePicker::make('end_date')
                            ->label(__('translate.ad_request.end_date'))
                            ->required(),
                        Textarea::make('additional_info')
                            ->label(__('translate.ad_request.additional_info')),
                        TextInput::make('investment_amount')
                            ->label(__('translate.ad_request.investment_amount'))
                            ->numeric()
                            ->required(),
                        FileUpload::make('payment_receipt')
                            ->label(__('translate.ad_request.payment_receipt'))
                            ->multiple()
                            ->columnSpanFull()
                            ->downloadable()
                            ->disk('azure_public')
                            ->visibility('public')
                            ->directory('pautas/' . now()->format('Y/m/d'))
                            ->maxFiles(5),
                    ]),

                Section::make(__('resources.ad_request.section_scheduled'))
                    ->columns(3)
                    ->description(__('resources.ad_request.section_description2'))
                    ->schema([
                        Select::make('status')
                            ->label(__('translate.ad_request.status'))
                            ->options(__('translate.ad_request.options_status'))
                            ->required(),
                        TextInput::make('messages_received')
                            ->label(__('translate.ad_request.messages_received'))
                            ->integer()
                            ->default(0),
                        TextInput::make('views')
                            ->label(__('translate.ad_request.views'))
                            ->integer()
                            ->default(0),
                        TextInput::make('reach')
                            ->label(__('translate.ad_request.reach'))
                            ->integer()
                            ->default(0),
                        TextInput::make('link_clicks')
                            ->label(__('translate.ad_request.link_clicks'))
                            ->integer()
                            ->default(0),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('translate.ad_request.user_id'))
                    ->searchable(),
                TextColumn::make('platform')
                    ->label(__('translate.ad_request.platform'))
                    ->alignCenter()
                    ->formatStateUsing(fn($state) => __('translate.ad_request.options_platform.' . $state))
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'facebook' => 'info',
                        'instagram' => 'danger',
                        'tiktok' => 'warning',
                        'youtube' => 'danger',
                    }),
                TextColumn::make('ad_url')
                    ->label(__('translate.ad_request.ad_url'))
                    ->url(fn($record) => $record->ad_url)
                    ->openUrlInNewTab()
                    ->limit(50)
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('target_age')
                    ->label(__('translate.ad_request.target_age'))
                    ->searchable(),
                TextColumn::make('target_location')
                    ->label(__('translate.ad_request.target_location'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('start_date')
                    ->label(__('translate.ad_request.start_date'))
                    ->date()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('end_date')
                    ->label(__('translate.ad_request.end_date'))
                    ->date()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('additional_info')
                    ->label(__('translate.ad_request.additional_info'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('investment_amount')
                    ->label(__('translate.ad_request.investment_amount'))
                    ->alignRight()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('status')
                    ->label(__('translate.ad_request.status'))
                    ->alignCenter()
                    ->formatStateUsing(fn($state) => __('translate.ad_request.options_status.' . $state))
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'scheduled' => 'info',
                        'stopped' => 'danger',
                        'finished' => 'success',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'pending' => 'heroicon-o-clock',
                        'scheduled' => 'heroicon-o-calendar-date-range',
                        'stopped' => 'heroicon-o-minus-circle',
                        'finished' => 'heroicon-o-check-circle',
                    }),
                TextColumn::make('messages_received')
                    ->label(__('translate.ad_request.messages_received'))
                    ->alignRight()
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('views')
                    ->label(__('translate.ad_request.views'))
                    ->alignRight()
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('reach')
                    ->label(__('translate.ad_request.reach'))
                    ->alignRight()
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('link_clicks')
                    ->label(__('translate.ad_request.link_clicks'))
                    ->alignRight()
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('created_at')
                    ->label(__('translate.ad_request.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('updated_at')
                    ->label(__('translate.ad_request.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('platform')
                    ->label(__('translate.ad_request.platform'))
                    ->options(__('translate.ad_request.options_platform')),
            ])
            ->actions([
                ActionGroup::make([
                    CommentsAction::make()
                        ->color('info'),
                    Tables\Actions\EditAction::make()
                        ->color('warning'),
                    Tables\Actions\Action::make('payWithStripe')
                        ->label('Pagar con TC/TD')
                        ->icon('heroicon-o-credit-card')
                        ->color('success')
                        ->url(fn(AdRequest $record) => route('ad-requests.pay', $record))
                        ->openUrlInNewTab()
                        ->visible(fn(AdRequest $record) => $record->investment_amount > 0 && $record->stripe_payment_status !== 'paid'),
                    Tables\Actions\DeleteAction::make(),
                ]),
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
            'index' => Pages\ListAdRequests::route('/'),
            'create' => Pages\CreateAdRequest::route('/create'),
            'edit' => Pages\EditAdRequest::route('/{record}/edit'),
        ];
    }
}
