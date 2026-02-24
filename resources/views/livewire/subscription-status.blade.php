<div class="mt-4">
    <x-filament::section>
        <x-slot name="heading">
            Mi Membresía
        </x-slot>

        @if(!$isSubscribed)
            <div class="flex flex-col items-center justify-center p-4">
                <p class="text-gray-500 mb-4">No tienes una membresía activa.</p>
                <x-filament::button tag="a" href="{{ route('filament.personal.pages.memberships') }}">
                    Ver Planes
                </x-filament::button>
            </div>
        @else
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Plan Actual</p>
                        <h3
                            class="text-xl font-bold text-{{ $planColor == 'info' ? 'blue' : ($planColor == 'warning' ? 'orange' : ($planColor == 'success' ? 'green' : 'gray')) }}-600">
                            {{ $planName }}
                        </h3>
                    </div>
                    <div>
                        @if($onGracePeriod)
                            <x-filament::badge color="warning">
                                Cancelado (Termina el {{ $user->subscription('default')->ends_at->format('d/m/Y') }})
                            </x-filament::badge>
                        @elseif($user->subscription('default')->canceled())
                            <x-filament::badge color="danger">
                                Cancelado
                            </x-filament::badge>
                        @else
                            <x-filament::badge color="success">
                                Activo
                            </x-filament::badge>
                        @endif
                    </div>
                </div>

                <div class="flex gap-4 mt-6 pt-4 border-t">
                    <x-filament::button tag="a" color="gray" href="{{ route('filament.personal.pages.memberships') }}">
                        Cambiar Plan
                    </x-filament::button>

                    @if($onGracePeriod)
                        <x-filament::button wire:click="resume" color="success">
                            Reanudar Suscripción
                        </x-filament::button>
                    @else
                        {{ $this->cancelSubscriptionAction }}
                    @endif
                </div>
            </div>
        @endif
    </x-filament::section>

    <x-filament-actions::modals />
</div>
