<x-filament::section heading="Đơn hàng">
    <div class="flex flex-col gap-2">
        <x-filament::link :href="route('filament.admin.resources.orders.mine')">
            Đơn hàng của tôi
        </x-filament::link>
        <x-filament::link :href="route('filament.admin.resources.orders.customers')">
            Đơn của khách hàng
        </x-filament::link>
    </div>
</x-filament::section>


