@assets
    @vite(['resources/css/app.css'])
@endassets
<x-filament::section>
    @if ($memberships->count() > 0)
        <div class="flex flex-col gap-4 w-full">
            @foreach ($memberships as $membership)
                <div class="flex md:flex-row flex-col md:space-x-4 w-full  h-fit">
                    <div class="max-w-sm">
                        @if ($membership->status === \App\Enums\CommonConstant::ACTIVE)
                            <x-filament::badge size="xs" color="success">
                                Đang hoạt động
                            </x-filament::badge>
                        @elseif($membership->status === \App\Enums\CommonConstant::INACTIVE)
                            <x-filament::badge size="xs" color="warning">
                                Chưa kích hoạt
                            </x-filament::badge>
                        @endif
                        @if ($membership->membershipPlan->is_testing)
                            <x-filament::badge size="xs" class="my-2" color="info">Gói dùng
                                thử</x-filament::badge>
                        @endif
                        <div class="p-2">
                            <div class="flex flex-col gap-4">
                                {{-- Tiêu đề --}}
                                <h5 class="text-xl font-semibold tracking-tight text-gray-900 dark:text-white">
                                    {{ $membership->membershipPlan->name }}
                                </h5>
                                {{-- Điểm --}}
                                <h6 class="text-3xl font-bold text-gray-900 dark:text-white">
                                    {{ $membership->membershipPlan->price }}
                                    POINT
                                    <span class="text-sm text-gray-500 dark:text-white">/
                                        {{ $membership->membershipPlan->duration }} tháng</span>
                                </h6>
                                <p class="text-gray-900 dark:text-white">
                                    {{ $membership->membershipPlan->description }}
                                </p>
                                <div
                                    class="block w-full max-w-sm bg-white border border-gray-200 rounded-lg dark:bg-gray-800 dark:border-gray-700 p-2">
                                    <ul
                                        class="max-w-md space-y-1 text-gray-500 list-disc list-inside dark:text-gray-400">
                                        <li>
                                            Ngày bắt
                                            đầu
                                            {{ \Illuminate\Support\Carbon::make($membership->start_date)->format('d/m/Y') }}
                                        </li>
                                        <li>
                                            Ngày kết
                                            thúc
                                            {{ \Illuminate\Support\Carbon::make($membership->end_date)->format('d/m/Y') }}
                                        </li>
                                    </ul>
                                </div>
                                <div
                                    class="block w-full max-w-sm bg-white border border-gray-200 rounded-lg dark:bg-gray-800 dark:border-gray-700 p-2">
                                    <ul class="max-w-md space-y-1 text-gray-500 list-inside dark:text-gray-400">
                                        @if ($membership->membershipPlan->config['featured_listing'])
                                            <li class="flex items-center">
                                                <svg class="w-3.5 h-3.5 me-2 text-green-500 dark:text-green-400 shrink-0"
                                                    aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                    fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                                                </svg>
                                                Sản phẩm được bán hiển thị ở vị trí nổi bật
                                            </li>
                                        @endif
                                        @if ($membership->membershipPlan->config['priority_support'])
                                            <li class="flex items-center">
                                                <svg class="w-3.5 h-3.5 me-2 text-green-500 dark:text-green-400 shrink-0"
                                                    aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                    fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                                                </svg>
                                                Được ưu tiên hỗ trợ khi có vấn đề
                                            </li>
                                        @endif
                                        @if ($membership->membershipPlan->config['discount_percentage'] > 0)
                                            <li class="flex items-center">
                                                <svg class="w-3.5 h-3.5 me-2 text-green-500 dark:text-green-400 shrink-0"
                                                    aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                    fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                                                </svg>
                                                Giảm {{ $membership->membershipPlan->config['discount_percentage'] }} %
                                                khi
                                                mua sản phẩm
                                            </li>
                                        @endif
                                        @if ($membership->membershipPlan->config['free_product_listing'])
                                            <li class="flex items-center">
                                                <svg class="w-3.5 h-3.5 me-2 text-green-500 dark:text-green-400 shrink-0"
                                                    aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                    fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                                                </svg>
                                                Đăng bán sản phẩm miễn phí
                                            </li>
                                        @elseif($membership->membershipPlan->config['max_products_per_month'] > 0)
                                            <li class="flex items-center">
                                                <svg class="w-3.5 h-3.5 me-2 text-green-500 dark:text-green-400 shrink-0"
                                                    aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                    fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                                                </svg>
                                                Miễn phí đăng
                                                bán {{ $membership->membershipPlan->config['max_products_per_month'] }}
                                                sản
                                                phẩm/ tháng
                                            </li>
                                        @endif
                                        @if ($membership->membershipPlan->config['free_auction_participation'])
                                            <li class="flex items-center">
                                                <svg class="w-3.5 h-3.5 me-2 text-green-500 dark:text-green-400 shrink-0"
                                                    aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                    fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                                                </svg>
                                                Tham gia trả giá miễn phí
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @php
                            $checkVal = $allMemberships->contains(
                                fn($plan) => $plan->price > $membership->price ||
                                    $plan->duration > $membership->duration,
                            );
                        @endphp

                        @if ($membership->status == \App\Enums\CommonConstant::ACTIVE || $membership->end_date < now())
                            <div class="fi-ta-actions flex shrink-0 items-center gap-3 flex-wrap justify-center">
                                <x-filament::button icon="heroicon-m-arrow-up-circle"
                                    wire:click="goToUpgradeMembership()" class="my-4">
                                    Nâng cấp hoặc gia hạn gói thành viên
                                </x-filament::button>
                            </div>
                        @endif

                        {{-- @if ($this->shouldShowButtonActive($membership))
                                <x-filament::button wire:click="activeMembership('{{$membership->id}}}')" class="w-full md:mb-0 mb-2">
                                    Kích hoạt lại gói thành viên
                                </x-filament::button>
                            @endif --}}
                    </div>
                    <div class="space-y-3 flex-1">
                        @foreach ($membership->membershipTransaction->sortByDesc('created_at') as $transaction)
                            <div
                                class="p-2 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                                <h5 class="mb-2 text-lg font-bold tracking-tight text-gray-900 dark:text-white">
                                    Giao dịch #{{ $transaction->id }}
                                </h5>
                                <p class="mb-3 font-normal text-gray-500 dark:text-gray-400">
                                    Ngày giao
                                    dịch:
                                    {{ \Illuminate\Support\Carbon::make($transaction->created_at)->format('d/m/Y H:i') }}
                                </p>
                                <p class="mb-3 font-normal text-gray-500 dark:text-gray-400">

                                    @if ($transaction->transaction_code == 'PAY BY POINTS')
                                        Số điểm: {{ number_format($transaction->money, 0, ',', '.') }}
                                        Đ
                                    @else
                                        Số tiền: {{ number_format($transaction->money, 0, ',', '.') }}
                                        VND
                                    @endif
                                </p>
                                <p class="font-normal text-gray-500 dark:text-gray-400">
                                    Trạng
                                    thái:
                                    {{ \App\Enums\Membership\MembershipTransactionStatus::getLabel($transaction->status) }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="px-6 py-12">
            <div class="mx-auto grid max-w-lg justify-items-center text-center">
                <div class="mb-4 rounded-full bg-gray-100 p-3 dark:bg-gray-500/20">
                    <svg class="h-6 w-6 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                    </svg>
                </div>
                <h4 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                    Bạn chưa đăng ký gói thành viên nào
                </h4>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Hãy đăng ký một gói thành viên để sử dụng các tính năng ưu đãi.
                </p>


                <div class="fi-ta-actions flex shrink-0 items-center gap-3 flex-wrap justify-center mt-6">
                    <x-filament::button icon="heroicon-m-user-group" wire:click="goToBuyMembership" class="mt-4">
                        Mua gói thành viên
                    </x-filament::button>
                </div>

            </div>
        </div>
    @endif
</x-filament::section>
