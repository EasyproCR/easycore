<div x-data="{ open: false, isLoaded: false }" class="z-50 flex flex-col items-end space-y-4"
    style="z-index: 9999; position: fixed; bottom: 1.5rem; right: 1.5rem;">
    <!-- Iframe Container -->
    <div x-show="open" x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 scale-95"
        class="bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-200 flex flex-col"
        style="display: none; width: 380px; height: 600px; max-height: 85vh; max-width: 90vw; margin-bottom: 1rem;">
        <!-- The template tag prevents iframe from loading until clicked -->
        <template x-if="isLoaded">
            <iframe src="{{ route('easychat') }}" class="w-full h-full border-0"
                allow="microphone; clipboard-read; clipboard-write"></iframe>
        </template>
    </div>

    <!-- Floating Button -->
    <button @click="open = !open; if(open) isLoaded = true"
        class="text-white rounded-full p-4 shadow-lg flex items-center justify-center transition-all duration-300"
        style="background-color: #ea580c; width: 64px; height: 64px;">
        <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
            stroke="currentColor" style="width: 32px; height: 32px;">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" />
        </svg>

        <svg x-show="open" style="display: none; width: 32px; height: 32px;" xmlns="http://www.w3.org/2000/svg"
            fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
        </svg>
    </button>
</div>