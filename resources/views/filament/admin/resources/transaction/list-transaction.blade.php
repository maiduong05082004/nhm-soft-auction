<x-filament-panels::page>
    <x-filament::section
        collapsible
        collapsed
        icon="heroicon-o-user-group"
    >
        <x-slot name="heading">
            Giao dịch thanh toán gói hội viên
        </x-slot>
        <x-slot name="description">
            Đây là danh sách giao dịch thanh toán gói hội viên của khách hàng.
        </x-slot>
        @livewire('filament.transaction-admin.membership')
    </x-filament::section>

    <x-filament::section
        collapsible
        collapsed
        icon="heroicon-o-user-group"
    >
        <x-slot name="heading">
            Giao dịch thanh toán điểm
        </x-slot>
        <x-slot name="description">
            Đây là danh sách giao dịch thanh toán điểm của khách hàng.
        </x-slot>
        @livewire('filament.transaction-admin.point-package')
    </x-filament::section>

</x-filament-panels::page>
