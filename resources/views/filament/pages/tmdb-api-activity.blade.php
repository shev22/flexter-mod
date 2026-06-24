<x-filament-panels::page>
    <div class="space-y-6">
        <div class="max-w-xs">
            {{ $this->form }}
        </div>

        <div class="grid grid-cols-2 gap-4 md:grid-cols-5">
            <x-filament::section>
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Movies Fetched</div>
                <div class="mt-1 text-2xl font-semibold">{{ number_format($this->dayStats['movies']) }}</div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Series Fetched</div>
                <div class="mt-1 text-2xl font-semibold">{{ number_format($this->dayStats['tv']) }}</div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Actors Fetched</div>
                <div class="mt-1 text-2xl font-semibold">{{ number_format($this->dayStats['actors']) }}</div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">HTTP Requests</div>
                <div class="mt-1 text-2xl font-semibold">{{ number_format($this->dayStats['requests']) }}</div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Activities</div>
                <div class="mt-1 text-2xl font-semibold">{{ number_format($this->dayStats['activities']) }}</div>
            </x-filament::section>
        </div>

        {{ $this->table }}
    </div>
</x-filament-panels::page>
