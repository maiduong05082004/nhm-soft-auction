<form wire:submit="updateConfig" class="flex flex-col gap-4">
    @foreach($this->configList as $index => $config)
        <div class="flex flex-col items-start gap-2">
            <label for="config_{{ $config->config_key }}"
                   class="block text-sm font-bold text-gray-700">{{ $config->config_key }}</label>
            <x-filament::input.wrapper class="w-full">
                <x-filament::input
                    wire:model="config_value.{{$config->config_key}}"
                />
            </x-filament::input.wrapper>
            <p class="block text-sm italic text-gray-500">
                Chú thích: {{$config->description}}
            </p>
        </div>
        @if(!$loop->last)
            <hr class="my-4">
        @endif
    @endforeach
    <x-filament::button
        type="submit"
        icon="heroicon-m-pencil"
        wire:loading.attr="disabled"
    >
        Chỉnh sửa
        <div wire:loading>
            <x-filament::loading-indicator class="h-5 w-5" />
        </div>
    </x-filament::button>
</form>

