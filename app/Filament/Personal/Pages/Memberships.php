<?php

namespace App\Filament\Personal\Pages;

use Filament\Pages\Page;

class Memberships extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Membresías';

    protected static ?string $slug = 'memberships';

    protected static string $view = 'filament.personal.pages.memberships';

    protected static string $layout = 'filament-panels::components.layout.base';

    public function getViewData(): array
    {
        return [
            'plans' => config('plans'),
        ];
    }

    public function checkout(string $planId)
    {
        $user = auth()->user();

        // Check if already subscribed to this plan
        if ($user->subscribed('default') && $user->subscription('default')->hasPrice($planId)) {
            \Filament\Notifications\Notification::make()
                ->title('Ya estás suscrito a este plan.')
                ->warning()
                ->send();
            return;
        }

        // Swap if already subscribed to another plan
        if ($user->subscribed('default')) {
            $user->subscription('default')->swap($planId);

            \Filament\Notifications\Notification::make()
                ->title('Plan cambiado exitosamente.')
                ->success()
                ->send();

            return redirect()->route('filament.personal.pages.memberships');
        }

        // New subscription
        return $user->newSubscription('default', $planId)
            ->checkout([
                'success_url' => route('filament.personal.pages.memberships'),
                'cancel_url' => route('filament.personal.pages.memberships'),
            ]);
    }
}
