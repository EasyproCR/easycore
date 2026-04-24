<?php

namespace App\Filament\Gerente\Resources\BillingControlResource\Pages;

use App\Filament\Gerente\Resources\BillingControlResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBillingControls extends ListRecords
{
    protected static string $resource = BillingControlResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
