<?php

namespace App\Filament\Personal\Resources\AdminReminderResource\Pages;

use App\Filament\Personal\Resources\AdminReminderResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditAdminReminder extends EditRecord
{
    protected static string $resource = AdminReminderResource::class;

    // No delete button — the user can only view the reminder and register results
    protected function getHeaderActions(): array
    {
        return [];
    }

    // Hide the default "Save" button — nothing to save in the read-only form
    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()->hidden();
    }

    // Hide the "Cancel" button
    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()->hidden();
    }
}
