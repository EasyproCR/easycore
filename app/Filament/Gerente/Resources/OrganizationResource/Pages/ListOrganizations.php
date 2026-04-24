<?php

namespace App\Filament\Gerente\Resources\OrganizationResource\Pages;

use App\Filament\Gerente\Resources\OrganizationResource;
use App\Models\Organization;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Colors\Color;

class ListOrganizations extends ListRecords
{
    protected static string $resource = OrganizationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array {
        return [
            null => Tab::make(__('resources.organization.tab_total_organizations'))
                ->badge($this->orderByOrganizationType() ?? 0)
                ->badgeColor(Color::Amber),
            Tab::make(__('resources.organization.tab_banks'))
                ->query(fn ($query) => $query->where('organization_type', 'banks'))
                ->badge($this->orderByOrganizationType('banks') ?? 0)
                ->badgeColor(Color::Red),
            Tab::make(__('resources.organization.tab_cooperatives'))
                ->query(fn ($query) => $query->where('organization_type', 'cooperatives'))
                ->badge($this->orderByOrganizationType('cooperatives') ?? 0)
                ->badgeColor(Color::Sky),
            Tab::make(__('resources.organization.tab_financial_institutions'))
                ->query(fn ($query) => $query->where('organization_type', 'financial_institutions'))
                ->badge($this->orderByOrganizationType('financial_institutions') ?? 0)
                ->badgeColor(Color::Blue),
            Tab::make(__('resources.organization.tab_associations'))
                ->query(fn ($query) => $query->where('organization_type', 'associations'))
                ->badge($this->orderByOrganizationType('associations') ?? 0)
                ->badgeColor(Color::Orange),
            Tab::make(__('resources.organization.tab_funds'))
                ->query(fn ($query) => $query->where('organization_type', 'funds'))
                ->badge($this->orderByOrganizationType('funds') ?? 0)
                ->badgeColor(Color::hex('#4fa230')),
            Tab::make(__('resources.organization.tab_development_and_others'))
                ->query(fn ($query) => $query->where('organization_type', 'development_and_others'))
                ->badge($this->orderByOrganizationType('development_and_others') ?? 0)
                ->badgeColor(Color::Fuchsia),
            Tab::make(__('resources.organization.tab_rent_a_car'))
                ->query(fn ($query) => $query->where('organization_type', 'rent_a_car'))
                ->badge($this->orderByOrganizationType('rent_a_car') ?? 0)
                ->badgeColor(Color::Green),
        ];
    }

    public function orderByOrganizationType(?string $type = null){
        if (blank($type)) {
            return Organization::count();
        }
        return Organization::where('organization_type', $type)->count();
    }
}
