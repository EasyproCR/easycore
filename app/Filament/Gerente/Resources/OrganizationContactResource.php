<?php

namespace App\Filament\Gerente\Resources;

use App\Filament\Gerente\Resources\OrganizationContactResource\Pages;
use App\Filament\Gerente\Resources\OrganizationContactResource\RelationManagers;
use App\Models\OrganizationContact;
use Filament\Forms;
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

class OrganizationContactResource extends Resource
{
    protected static ?string $model = OrganizationContact::class;

    protected static ?string $navigationLabel = null;
    protected static ?string $navigationGroup = null;

    public static function getLabel(): ?string
    {
        return __('resources.organization_contact.label');
    }

    public static function getPluralLabel(): ?string
    {
        return __('resources.organization_contact.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.organization_contact.navigation');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('resources.organization.navigation_group');
    }

    protected static ?int $navigationSort = 8;

    protected static ?string $navigationIcon = 'heroicon-o-envelope-open';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('resources.organization_contact.sectionContact'))
                ->columns(3)
                ->schema([
                    Select::make('organization_id')
                        ->label(__('translate.organization_contact.organization_id'))
                        ->relationship(
                            name: 'organization',
                            titleAttribute: 'organization_name'
                        ),
                    Select::make('contact_type')
                        ->label(__('translate.organization_contact.contact_type'))
                        ->options([
                            'adjudicated_assets' => __('translate.organization_contact.option_contact_type.0'),
                            'cotizations' => __('translate.organization_contact.option_contact_type.1'),
                            'billing' => __('translate.organization_contact.option_contact_type.2'),
                        ]),
                    TextInput::make('contact_name')
                        ->label(__('translate.organization_contact.contact_name'))
                        ->maxLength(100),
                    TextInput::make('contact_position')
                        ->label(__('translate.organization_contact.contact_position'))
                        ->maxLength(100),
                    TextInput::make('contact_phone_number')
                        ->label(__('translate.organization_contact.contact_phone_number'))
                        ->maxLength(20),
                    TextInput::make('contact_email')
                        ->label(__('translate.organization_contact.contact_email'))
                        ->maxLength(255)
                        ->email(),
                    Select::make('contact_main_method')
                        ->label(__('translate.organization_contact.contact_main_method'))
                        ->options([
                            'email' => __('translate.organization_contact.option_contact_main_method.0'),
                            'whatsapp' => __('translate.organization_contact.option_contact_main_method.1'),
                            'both' => __('translate.organization_contact.option_contact_main_method.2'),
                        ]),
                    Textarea::make('contact_remarks')
                        ->label(__('translate.organization_contact.contact_remarks'))
                        ->columnSpan(2),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('organization.organization_name')
                    ->label(__('translate.organization_contact.organization_id'))
                    ->searchable()
                    ->alignCenter(),
                TextColumn::make('contact_name')
                    ->label(__('translate.organization_contact.contact_name'))
                    ->searchable()
                    ->alignCenter(),
                TextColumn::make('contact_position')
                    ->label(__('translate.organization_contact.contact_position'))
                    ->searchable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('contact_phone_number')
                    ->label(__('translate.organization_contact.contact_phone_number'))
                    ->searchable()
                    ->alignCenter(),
                TextColumn::make('contact_email')
                    ->label(__('translate.organization_contact.contact_email'))
                    ->searchable()
                    ->alignCenter(),
                TextColumn::make('contact_main_method')
                    ->label(__('translate.organization_contact.contact_main_method'))
                    ->searchable()
                    ->alignCenter()
                    ->formatStateUsing(function ($state){
                        return match ($state) {
                            'email' => __('translate.organization_contact.option_contact_main_method.0'),
                            'whatsapp' => __('translate.organization_contact.option_contact_main_method.1'),
                            'both' => __('translate.organization_contact.option_contact_main_method.2'),
                        };
                    })
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('contact_remarks')
                    ->label(__('translate.organization_contact.contact_remarks'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignCenter(),
                TextColumn::make('created_at')
                    ->label(__('translate.organization_contact.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('translate.organization_contact.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('organization_id')
                    ->label(__('translate.organization_contact.organization_id'))
                    ->relationship(
                        name: 'organization',
                        titleAttribute: 'organization_name'
                    ),
                SelectFilter::make('contact_main_method')
                    ->label(__('translate.organization_contact.contact_main_method'))
                    ->options([
                        'email' => __('translate.organization_contact.option_contact_main_method.0'),
                        'whatsapp' => __('translate.organization_contact.option_contact_main_method.1'),
                        'both' => __('translate.organization_contact.option_contact_main_method.2'),
                    ]),
            ])
            ->actions([
                ActionGroup::make([
                    CommentsAction::make()
                        ->color('info'),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()
                        ->color('warning'),
                    Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListOrganizationContacts::route('/'),
            'create' => Pages\CreateOrganizationContact::route('/create'),
            'edit' => Pages\EditOrganizationContact::route('/{record}/edit'),
        ];
    }
}
