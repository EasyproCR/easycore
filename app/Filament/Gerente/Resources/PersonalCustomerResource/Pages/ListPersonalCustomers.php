<?php

namespace App\Filament\Gerente\Resources\PersonalCustomerResource\Pages;

use App\Filament\Gerente\Resources\PersonalCustomerResource;
use App\Models\PersonalCustomer;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Colors\Color;

class ListPersonalCustomers extends ListRecords
{
    protected static string $resource = PersonalCustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array {
        return [
            null => Tab::make(__('resources.customer.tab_total_customers'))
                ->badge($this->orderByCustomerType() ?? 0)
                ->badgeColor(Color::Orange),
            Tab::make(__('resources.customer.tab_buyer'))
                ->query(fn ($query) => $query->where('customer_type', 'buyer'))
                ->badge($this->orderByCustomerType('buyer') ?? 0)
                ->badgeColor(Color::Amber),
            Tab::make(__('resources.customer.tab_seller'))
                ->query(fn ($query) => $query->where('customer_type', 'seller'))
                ->badge($this->orderByCustomerType('seller') ?? 0)
                ->badgeColor(Color::Indigo),
            Tab::make(__('resources.customer.tab_investor'))
                ->query(fn ($query) => $query->where('customer_type', 'investor'))
                ->badge($this->orderByCustomerType('investor') ?? 0)
                ->badgeColor(Color::Green),
            Tab::make(__('resources.customer.tab_tenant'))
                ->query(fn ($query) => $query->where('customer_type', 'tenant'))
                ->badge($this->orderByCustomerType('tenant') ?? 0)
                ->badgeColor(Color::Teal),
            Tab::make(__('resources.customer.tab_other'))
                ->query(fn ($query) => $query->where('customer_type', 'other'))
                ->badge($this->orderByCustomerType('other') ?? 0)
                ->badgeColor(Color::Neutral),
        ];
    }

    public function orderByCustomerType(?string $type = null) {
        if(blank($type)){
            return PersonalCustomer::count();
        }
        return PersonalCustomer::where('customer_type', $type)->count();
    }
}
