<?php

namespace App\Filament\Personal\Resources\CustomerReportResource\Pages;

use App\Filament\Personal\Resources\CustomerReportResource;
use App\Helpers\FilamentUrlHelper;
use App\Mail\ReportStatusMail;
use App\Models\Organization;
use App\Models\PersonalCustomer;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class CreateCustomerReport extends CreateRecord
{
    protected static string $resource = CustomerReportResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->record;

        $organization = Organization::find($record->organization_id);
        $customer = PersonalCustomer::find($record->personal_customer_id);
        $solicitante = $record->user;

        $iso2 = $solicitante?->country?->iso2;

        if ($iso2 === 'PA' || $iso2 === 'SV') {
            $supportEmail = $iso2 === 'PA' ? 'soportepa@g-easypro.com' : 'soportesv@g-easypro.com';

            $dataToSend = [
                'property_name'         => $record->property_name,
                'organization'          => $organization?->organization_name,
                'name'                  => $solicitante?->name,
                'email'                 => $solicitante?->email,
                'customer_name'         => $customer?->full_name,
                'customer_national_id'  => $customer?->national_id,
                'url'                   => FilamentUrlHelper::getResourceUrlForPanel(
                    'soporte',
                    CustomerReportResource::class,
                    $record,
                ),
            ];

            Mail::to(new Address($supportEmail))
                ->send(new ReportStatusMail($dataToSend, 'pending'));
        } else {
            // administrative users (primary recipients)
            $userRoles = User::role(['soporte', 'ventas'])->get();

            foreach ($userRoles as $destinatario) {
                $dataToSend = [
                    'property_name'         => $record->property_name,
                    'organization'          => $organization?->organization_name,
                    'name'                  => $solicitante?->name,
                    'email'                 => $solicitante?->email,
                    'customer_name'         => $customer?->full_name,
                    'customer_national_id'  => $customer?->national_id,
                    'url'                   => FilamentUrlHelper::getResourceUrl(
                        $destinatario, // the recipient
                        CustomerReportResource::class,
                        $record,
                    ),
                ];

                Mail::to(new Address($destinatario->email))
                    ->send(new ReportStatusMail($dataToSend, 'pending'));
            }
        }

        // Also send to the requester (if they are a panel_user)
        if ($solicitante && $solicitante->hasRole('panel_user')) {
            $dataToSend = [
                'property_name'         => $record->property_name,
                'organization'          => $organization?->organization_name,
                'name'                  => $solicitante?->full_name,
                'email'                 => $solicitante?->email,
                'customer_name'         => $customer?->full_name,
                'customer_national_id'  => $customer?->national_id,
                'url'                   => FilamentUrlHelper::getResourceUrl(
                    $solicitante,
                    CustomerReportResource::class,
                    $record,
                ),
            ];

            Mail::to(new Address($solicitante->email))
                ->send(new ReportStatusMail($dataToSend, 'pending'));
        }
    }
}
