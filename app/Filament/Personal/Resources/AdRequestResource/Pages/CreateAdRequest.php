<?php

namespace App\Filament\Personal\Resources\AdRequestResource\Pages;

use App\Filament\Personal\Resources\AdRequestResource;
use App\Helpers\FilamentUrlHelper;
use App\Mail\AdStatusMail;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class CreateAdRequest extends CreateRecord
{
    protected static string $resource = AdRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->record;
        $solicitante = $record->user;
        $supportEmail = 'rrhh@g-easypro.com';

        $dataToSend = [
            'name' => $solicitante?->name,
            'email' => $solicitante?->email,
            'platform' => $record->platform,
            'start_date' => $record->start_date,
            'end_date' => $record->end_date,
            'url' => FilamentUrlHelper::getResourceUrlForPanel(
                'rrhh',
                AdRequestResource::class,
                $record,
            ),
        ];

        Mail::to(new Address($supportEmail))
            ->send(new AdStatusMail($dataToSend, 'pending'));

        if ($solicitante && $solicitante->hasRole('panel_user')) {
            $dataToSend = [
                'name' => $solicitante?->full_name,
                'email' => $solicitante?->email,
                'platform' => $record->platform,
                'start_date' => $record->start_date,
                'end_date' => $record->end_date,
                'url' => FilamentUrlHelper::getResourceUrl(
                    $solicitante,
                    AdRequestResource::class,
                    $record,
                ),
            ];

            Mail::to(new Address($solicitante->email))
                ->send(new AdStatusMail($dataToSend, 'pending'));
        }
    }
}
