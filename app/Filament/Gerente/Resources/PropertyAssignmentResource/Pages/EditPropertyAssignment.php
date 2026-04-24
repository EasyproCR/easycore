<?php

namespace App\Filament\Gerente\Resources\PropertyAssignmentResource\Pages;

use App\Filament\Gerente\Resources\PropertyAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPropertyAssignment extends EditRecord
{
    protected static string $resource = PropertyAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
