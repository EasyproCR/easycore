<?php

namespace App\Filament\Personal\Resources\OfferResource\Pages;

use App\Filament\Personal\Resources\OfferResource;
use App\Helpers\FilamentUrlHelper;
use App\Mail\OfferStatusMail;
use App\Models\Organization;
use App\Models\PersonalCustomer;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class CreateOffer extends CreateRecord
{
    protected static string $resource = OfferResource::class;

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

        $amount = $this->record->offer_amount_usd
            ? '$' . number_format($this->record->offer_amount_usd, 2)
            : '₡' . number_format($this->record->offer_amount_crc, 2);

        $attachments = [];
        if (!empty($this->record->offer_files)) {
            foreach ($this->record->offer_files as $filePath) {
                $attachments[] = $filePath;
            }
        }

        // administrative users (primary recipients)
        $userRol = User::role(['soporte', 'ventas', 'servicio_al_cliente', 'gerente'])->get();

        foreach ($userRol as $destinatario) {
            $dataToSend = [
                'property'              => $record->property_name,
                'organization'          => $organization?->organization_name,
                'email'                 => $solicitante?->email,
                'customer_name'         => $customer?->full_name,
                'customer_national_id'  => $customer?->national_id,
                'offer_amount'          => $amount,
                'attachments'           => $attachments,
                'attachments_disk'      => config('filesystems.default'),
                'url'                   => FilamentUrlHelper::getResourceUrl(
                    $destinatario, // the recipient
                    OfferResource::class,
                    $record,
                ),
            ];

            Mail::to(new Address($destinatario->email))
                ->send(new OfferStatusMail($dataToSend, 'pending'));
        }

        // Also send to the requester (if they are a panel_user)
        if ($solicitante && $solicitante->hasRole('panel_user')) {
            $dataToSend = [
                'property'              => $record->property_name,
                'organization'          => $organization?->organization_name,
                'email'                 => $solicitante?->full_name,
                'customer_name'         => $customer?->full_name,
                'customer_national_id'  => $customer?->national_id,
                'offer_amount'          => $amount,
                'attachments'           => $attachments,
                'attachments_disk'      => config('filesystems.default'),
                'url'                   => FilamentUrlHelper::getResourceUrl(
                    $solicitante,
                    OfferResource::class,
                    $record,
                ),
            ];

            Mail::to(new Address($solicitante->email))
                ->send(new OfferStatusMail($dataToSend, 'pending'));
        }
    }
}
