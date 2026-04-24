<?php

namespace App\Filament\Gerente\Resources\AdminReminderResource\Pages;

use App\Filament\Gerente\Resources\AdminReminderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdminReminders extends ListRecords
{
    protected static string $resource = AdminReminderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
