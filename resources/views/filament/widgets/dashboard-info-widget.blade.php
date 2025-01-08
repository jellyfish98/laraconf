<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex space-x-6 justify-between">
            <p class="text-xl font-medium italic underline underline-offset-2">
                This is my Laraconf Dashboard Info Widget, Filament is awesome!
            </p>
            {{ $this->callNotification() }}
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
