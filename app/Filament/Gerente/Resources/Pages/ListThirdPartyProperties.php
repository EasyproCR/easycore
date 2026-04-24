<?php

namespace App\Filament\Gerente\Resources\ThirdPartyPropertyResource\Pages;

use App\Filament\Gerente\Resources\ThirdPartyPropertyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListThirdPartyProperties extends ListRecords
{
    protected static string $resource = ThirdPartyPropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
