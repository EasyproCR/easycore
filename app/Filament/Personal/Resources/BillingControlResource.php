<?php

namespace App\Filament\Personal\Resources;

use App\Filament\Personal\Resources\BillingControlResource\Pages;
use App\Filament\Personal\Resources\BillingControlResource\RelationManagers;
use App\Models\BillingControl;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
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
use Illuminate\Support\Facades\Date;
use Parallax\FilamentComments\Tables\Actions\CommentsAction;

class BillingControlResource extends Resource
{
    protected static ?string $model = BillingControl::class;

    protected static ?string $navigationLabel = null;
    protected static ?string $navigationGroup = null;

    public static function getLabel(): ?string
    {
        return __('resources.billing_control.label');
    }

    public static function getPluralLabel(): ?string
    {
        return __('resources.billing_control.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.billing_control.navigation');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('resources.customer.navigation_group');
    }

    protected static ?string $navigationIcon = 'heroicon-o-document-currency-dollar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('resources.billing_control.sectionBillingControl'))
                    ->columns(2)
                    ->schema([
                        Select::make('offer_id')
                            ->label(__('translate.billing_control.offer_id'))
                            ->relationship(
                                name: 'offer',
                                titleAttribute: 'property_name',
                                modifyQueryUsing: fn (Builder $query) => $query->where('offer_status', 'signed'),
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(),
                        Select::make('invoice_status')
                            ->label(__('translate.billing_control.invoice_status'))
                            ->options(__('translate.billing_control.options_invoice_status'))
                            ->required()
                            ->disabled(),
                        TextInput::make('payment_percentage')
                            ->label(__('translate.billing_control.payment_percentage'))
                            ->maxLength(5)
                            ->suffix('%')
                            ->required()
                            ->disabled(),
                        DatePicker::make('billing_date')
                            ->label(__('translate.billing_control.billing_date'))
                            ->required()
                            ->native(false)
                            ->disabled(),
                        DatePicker::make('funds_received_date')
                            ->label(__('translate.billing_control.funds_received_date'))
                            ->native(false)
                            ->disabled(),
                        FileUpload::make('invoice_files')
                            ->label(__('translate.billing_control.invoice_files'))
                            ->multiple()
                            ->downloadable()
                            ->disk('azure_public')
                            ->visibility('public')
                            ->directory('facturas/' .now()->format('Y/m/d'))
                            ->maxFiles(5)
                            ->disabled(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('offer.property_name')
                    ->label(__('translate.billing_control.offer_id'))
                    ->searchable(),
                TextColumn::make('offer.personal_customer.full_name')
                    ->label('Cliente')
                    ->searchable(),
                TextColumn::make('invoice_status')
                    ->label(__('translate.billing_control.invoice_status'))
                    ->alignCenter()
                    ->badge()
                    ->formatStateUsing(
                        fn($state) => __('translate.billing_control.options_invoice_status.' . $state)
                    )
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'invoiced' => 'info',
                        'paid_to_EasyPro' => 'success',
                        'paid_to_user' => 'success',
                    }),
                TextColumn::make('payment_percentage')
                    ->label(__('translate.billing_control.payment_percentage'))
                    ->formatStateUsing(fn($state) => $state . '%')
                    ->alignCenter(),
                TextColumn::make('billing_date')
                    ->label('Facturación')
                    ->date()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('funds_received_date')
                    ->label('Recepción de fondos')
                    ->date()
                    ->alignCenter()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label(__('translate.billing_control.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('updated_at')
                    ->label(__('translate.billing_control.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('invoice_status')
                    ->label(__('translate.billing_control.invoice_status'))
                    ->options(__('translate.billing_control.options_invoice_status')),
            ])
            ->actions([
                ActionGroup::make([
                    CommentsAction::make()
                        ->color('info'),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()
                        ->color('warning'),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (Auth::check()) {
            $query->whereHas('offer', function ($q) {
                $q->where('user_id', Auth::user()->id);
            });
        }

        return $query;
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
            'index' => Pages\ListBillingControls::route('/'),
            'edit' => Pages\EditBillingControl::route('/{record}/edit'),
        ];
    }
}
