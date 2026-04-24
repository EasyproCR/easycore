<?php

namespace App\Filament\Gerente\Resources\OrganizationContactResource\Pages;

use App\Filament\Gerente\Resources\OrganizationContactResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrganizationContact extends EditRecord
{
    protected static string $resource = OrganizationContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
