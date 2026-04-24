<?php

namespace App\Filament\Personal\Resources\PropertyAssignmentResource\Pages;

use App\Filament\Personal\Resources\PropertyAssignmentResource;
use App\Mail\AssignmentStatus\PropertyPending;
use App\Models\Organization;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailables\Address;

class CreatePropertyAssignment extends CreateRecord
{
    protected static string $resource = PropertyAssignmentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->record;

        $organization = Organization::find($record->organization_id);
        $user = $record->user;

        $dataToSend = [
            'property_info' => $record->property_info,
            'organization' => $organization?->organization_name,
            'name' => $user?->name,
            'email' => $user?->email,
        ];

        $iso2 = $user?->country?->iso2;

        if ($iso2 === 'PA' || $iso2 === 'SV') {
            $supportEmail = $iso2 === 'PA' ? 'kathia.garcia@g-easypro.com' : 'maria.castillo@g-easypro.com';

            Mail::to(new Address($supportEmail))
                ->send(new PropertyPending($dataToSend));
        } else {
            $userRoles = User::role('servicio_al_cliente')->pluck('email')->unique()->toArray();

            if (!empty($userRoles)) {
                Mail::to(array_map(fn($email) => new Address($email), $userRoles))
                    ->send(new PropertyPending($dataToSend));
            }
        }
    }
}
