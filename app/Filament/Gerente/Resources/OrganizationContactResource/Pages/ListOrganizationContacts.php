<?php

namespace App\Filament\Gerente\Resources\OrganizationContactResource\Pages;

use App\Filament\Gerente\Resources\OrganizationContactResource;
use App\Models\OrganizationContact;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListOrganizationContacts extends ListRecords
{
    protected static string $resource = OrganizationContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            null => Tab::make(__('resources.organization_contact.tab_total_contacts'))
                ->badge($this->orderbyOrganizationType() ?? 0),
            Tab::make(__('resources.organization_contact.tab_adjudicated_assets'))
                ->query(fn ($query) => $query->where('contact_type', 'adjudicated_assets'))
                ->badge($this->orderbyOrganizationType('adjudicated_assets') ?? 0),
            Tab::make(__('resources.organization_contact.tab_cotizations'))
                ->query(fn ($query) => $query->where('contact_type', 'cotizations'))
                ->badge($this->orderbyOrganizationType('cotizations') ?? 0),
            Tab::make(__('resources.organization_contact.tab_billing'))
                ->query(fn ($query) => $query->where('contact_type', 'billing'))
                ->badge($this->orderbyOrganizationType('billing') ?? 0),
        ];
    }

    public function orderbyOrganizationType(?string $type = null){
        if (blank($type)) {
            return OrganizationContact::count();
        }
        return OrganizationContact::where('contact_type', $type)->count();
    }
}
