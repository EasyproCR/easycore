<?php

namespace App\Filament\Sales\Resources\BillingControlResource\Pages;

use App\Filament\Sales\Resources\BillingControlResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBillingControls extends ListRecords
{
    protected static string $resource = BillingControlResource::class;

    public function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
