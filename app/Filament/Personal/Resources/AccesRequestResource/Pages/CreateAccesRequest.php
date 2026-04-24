<?php

namespace App\Filament\Personal\Resources\AccesRequestResource\Pages;

use App\Filament\Personal\Resources\AccesRequestResource;
use App\Helpers\FilamentUrlHelper;
use App\Mail\AccesStatusMail;
use App\Models\Organization;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailables\Address;

class CreateAccesRequest extends CreateRecord
{
    protected static string $resource = AccesRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->record;

        $organization = Organization::find($record->organization_id);
        $solicitante = $record->user;

        $iso2 = $solicitante?->country?->iso2;

        if ($iso2 === 'PA' || $iso2 === 'SV') {
            $supportEmail = $iso2 === 'PA' ? 'soportepa@g-easypro.com' : 'soportesv@g-easypro.com';

            $dataToSend = [
                'type_of_request' => __('translate.access_request.options_type_of_request.' . $record->type_of_request),
                'property'        => $record->property,
                'organization'    => $organization?->organization_name,
                'name'            => $solicitante?->name,
                'email'           => $solicitante?->email,
                'url'             => FilamentUrlHelper::getResourceUrlForPanel(
                    'soporte',
                    AccesRequestResource::class,
                    $record,
                ),
            ];

            Mail::to(new Address($supportEmail))
                ->send(new AccesStatusMail($dataToSend, 'pending'));
        } else {
            // administrative users (primary recipients)
            $userRoles = User::role(['soporte', 'ventas', 'servicio_al_cliente', 'rrhh'])->get();

            foreach ($userRoles as $destinatario) {
                $dataToSend = [
                    'type_of_request' => __('translate.access_request.options_type_of_request.' . $record->type_of_request),
                    'property'        => $record->property,
                    'organization'    => $organization?->organization_name,
                    'name'            => $solicitante?->name,
                    'email'           => $solicitante?->email,
                    // URL generated based on the recipient.
                    'url'             => FilamentUrlHelper::getResourceUrl(
                        $destinatario, // the recipient
                        AccesRequestResource::class,
                        $record,
                    ),
                ];

                Mail::to(new Address($destinatario->email))
                    ->send(new AccesStatusMail($dataToSend, 'pending'));
            }
        }

        // Also send to the requester (if they are a panel_user)
        if ($solicitante && $solicitante->hasRole('panel_user')) {
            $dataToSend = [
                'type_of_request' => __('translate.access_request.options_type_of_request.' . $record->type_of_request),
                'property'        => $record->property,
                'organization'    => $organization?->organization_name,
                'name'            => $solicitante?->full_name,
                'email'           => $solicitante?->email,
                'url'             => FilamentUrlHelper::getResourceUrl(
                    $solicitante,
                    AccesRequestResource::class,
                    $record,
                ),
            ];

            Mail::to(new Address($solicitante->email))
                ->send(new AccesStatusMail($dataToSend, 'pending'));
        }
    }
}
