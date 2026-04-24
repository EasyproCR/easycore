<?php

namespace App\Filament\Gerente\Resources;

use App\Filament\Gerente\Resources\OrganizationResource\Pages;
use App\Filament\Gerente\Resources\OrganizationResource\RelationManagers;
use App\Models\Organization;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Parallax\FilamentComments\Tables\Actions\CommentsAction;

class OrganizationResource extends Resource
{
    protected static ?string $model = Organization::class;

    protected static ?string $navigationLabel = null;
    protected static ?string $navigationGroup = null;

    public static function getLabel(): ?string
    {
        return __('resources.organization.label');
    }

    public static function getPluralLabel(): ?string
    {
        return __('resources.organization.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.organization.navigation');
    }

    public static function getNavigationGroup(): string
    {
        return __('resources.organization.navigation_group');
    }

    protected static ?int $navigationSort = 9;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('resources.organization.sectionOrganization'))
                    ->columns(2)
                    ->schema([
                        Select::make('organization_type')
                            ->label(__('translate.organization.organization_type'))
                            ->options([
                                'banks' => __('translate.organization.options_organization_type.0'),
                                'cooperatives' => __('translate.organization.options_organization_type.1'),
                                'financial_institutions' => __('translate.organization.options_organization_type.2'),
                                'associations' => __('translate.organization.options_organization_type.3'),
                                'funds' => __('translate.organization.options_organization_type.4'),
                                'development_and_others' => __('translate.organization.options_organization_type.5'),
                                'rent_a_car' => __('translate.organization.options_organization_type.6'),
                            ])
                            ->required(),
                        TextInput::make('organization_name')
                            ->label(__('translate.organization.organization_name'))
                            ->maxLength(150)
                            ->required(),
                    ]),
                Section::make(__('resources.organization.sectionOther'))
                    ->schema([
                        Textarea::make('asset_update_dates')
                            ->label(__('translate.organization.asset_update_dates')),
                        RichEditor::make('sugef_report')
                            ->label(__('translate.organization.sugef_report')),
                        RichEditor::make('offer_form')
                            ->label(__('translate.organization.offer_form')),
                        RichEditor::make('catalog_or_website')
                            ->label(__('translate.organization.catalog_or_website')),
                        RichEditor::make('vehicles_page')
                            ->label(__('translate.organization.vehicles_page')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('organization_name')
                    ->label(__('translate.organization.organization_name'))
                    ->searchable()
                    ->alignCenter(),
                TextColumn::make('sugef_report')
                    ->label(__('translate.organization.sugef_report'))
                    ->html()
                    ->alignCenter()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('offer_form')
                    ->html()
                    ->label(__('translate.organization.offer_form'))
                    ->alignCenter()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('catalog_or_website')
                    ->html()
                    ->label(__('translate.organization.catalog_or_website'))
                    ->alignCenter()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('vehicles_page')
                    ->html()
                    ->label(__('translate.organization.vehicles_page'))
                    ->alignCenter()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    CommentsAction::make()
                        ->color('info'),
                    ViewAction::make(),
                    EditAction::make()
                        ->color('warning'),
                    DeleteAction::make()
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
            'index' => Pages\ListOrganizations::route('/'),
            'create' => Pages\CreateOrganization::route('/create'),
            'edit' => Pages\EditOrganization::route('/{record}/edit'),
        ];
    }
}
