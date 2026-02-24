<?php

namespace App\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;

class SubscriptionStatus extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    public function cancelSubscriptionAction(): Action
    {
        return Action::make('cancelSubscription')
            ->label('Cancelar Suscripción')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Cancelar Suscripción')
            ->modalDescription('¿Estás seguro de que deseas cancelar tu suscripción? Perderás acceso a los beneficios al final del periodo.')
            ->modalSubmitActionLabel('Sí, cancelar')
            ->action(function () {
                auth()->user()->subscription('default')->cancel();

                \Filament\Notifications\Notification::make()
                    ->title('Suscripción cancelada.')
                    ->success()
                    ->send();
            });
    }

    public function resume()
    {
        auth()->user()->subscription('default')->resume();

        \Filament\Notifications\Notification::make()
            ->title('Suscripción reanudada.')
            ->success()
            ->send();
    }

    public function render()
    {
        $user = auth()->user();
        $isSubscribed = $user->subscribed('default');
        $planName = 'Ninguno';
        $planColor = 'gray';
        $renewalDate = null;
        $onGracePeriod = false;

        if ($isSubscribed) {
            $subscription = $user->subscription('default');
            $priceId = $subscription->stripe_price;
            $plans = config('plans');

            foreach ($plans as $plan) {
                if ($plan['id'] === $priceId) {
                    $planName = $plan['name'];
                    $planColor = $plan['color'];
                    break;
                }
            }

            $renewalDate = $subscription->ends_at
                ? $subscription->ends_at->format('d/m/Y')
                : ($subscription->asStripeSubscription()->current_period_end ?? null);

            if (is_int($renewalDate)) {
                $renewalDate = date('d/m/Y', $renewalDate);
            }

            $onGracePeriod = $subscription->onGracePeriod();
        }

        return view('livewire.subscription-status', [
            'isSubscribed' => $isSubscribed,
            'planName' => $planName,
            'planColor' => $planColor,
            'onGracePeriod' => $onGracePeriod,
            'user' => $user,
        ]);
    }
}
