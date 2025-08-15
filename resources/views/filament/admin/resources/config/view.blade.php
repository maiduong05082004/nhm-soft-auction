<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="description">
            Là nơi chứa tất cả các cấu hình hệ thống, mỗi cấu hình ở dưới đây đều ảnh hưởng đến hệ thống nên sẽ phải
            chỉnh sửa cẩn thân !
        </x-slot>
        {{-- Content --}}
        @livewire('filament.config-form')


    </x-filament::section>

</x-filament-panels::page>
