<div class="min-h-screen flex flex-col items-center justify-center p-6">
    <div class="w-full max-w-7xl">
        <div class="mb-8 flex justify-between items-center">
            <h1 class="text-3xl font-bold text-white">Planes de Suscripción</h1>
            <x-filament::button
                tag="a"
                href="{{ filament()->getUrl() }}"
                color="gray"
            >
                Volver al panel
            </x-filament::button>
        </div>
        <br>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($plans as $key => $plan)
                @php
                    $color = match ($plan['color']) {
                        'info' => 'border-t-4 border-blue-500',
                        'warning' => 'border-t-4 border-orange-500',
                        'success' => 'border-t-4 border-emerald-500',
                        default => 'border-t-4 border-gray-500',
                    };
                    $btnColor = match ($plan['color']) {
                        'info' => 'info',
                        'warning' => 'warning',
                        'success' => 'success',
                        default => 'gray',
                    };
                @endphp
                <div class="fi-section {{ $color }} rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 flex flex-col h-full p-6">
                    <div class="text-center flex flex-col h-full">
                        <h2 class="text-2xl font-bold mb-2">{{ $plan['name'] }}</h2>
                        <p class="text-3xl font-bold mb-8">${{ $plan['price'] / 100 }} <span
                                class="text-sm font-normal text-gray-500">/mes</span></p>
                        <br>
                        <ul class="text-left mb-8 space-y-4 flex-1">
                            @foreach($plan['features'] as $feature)
                                <li class="flex items-start">
                                    <x-heroicon-o-check class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" />
                                    <span class="text-sm">{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <br>
                        <x-filament::button wire:click="checkout('{{ $plan['id'] }}')" color="{{ $btnColor }}" class="w-full mt-auto">
                            Contratar
                        </x-filament::button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
