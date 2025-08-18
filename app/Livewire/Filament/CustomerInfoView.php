<?php

namespace App\Livewire\Filament;

use App\Services\Auth\AuthServiceInterface;
use App\Utils\HelperFunc;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Contracts\Auth\Authenticatable;
use Livewire\Component;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;

class CustomerInfoView extends Component implements HasForms, HasInfolists
{
    use InteractsWithInfolists;
    use InteractsWithForms;

    private AuthServiceInterface $service;

    public ?Authenticatable $auth;

    public function boot(AuthServiceInterface $service)
    {
        $this->service = $service;
    }
    public function mount(): void
    {
        $this->auth = $this->service->getInfoAuth();
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->auth)
            ->schema([
                Components\Section::make()
                    ->schema([
                        Components\Split::make([
                            Components\Grid::make(2)
                                ->schema([
                                    Components\Group::make([
                                        Components\TextEntry::make('name')
                                            ->label('Tên người dùng'),
                                        Components\TextEntry::make('email')
                                            ->label('Email'),
                                        Components\TextEntry::make('phone')
                                            ->label('Số điện thoại')
                                            ->default("Chưa cập nhật"),
                                        Components\TextEntry::make('created_at')
                                            ->label('Ngày tạo tài khoản')
                                            ->dateTime("d/m/Y H:i"),
                                    ]),
                                    Components\Group::make([
                                        Components\TextEntry::make('membership')->label('Membership')
                                            ->formatStateUsing(fn(bool $state): string => $state ? 'Membership' : 'Chưa đăng ký')
                                            ->badge()
                                            ->color(fn(bool $state): string => $state ? 'success' : 'danger'),
                                        Components\TextEntry::make('address')
                                            ->label('Địa chỉ')
                                            ->default("Chưa cập nhật"),
                                        Components\TextEntry::make('introduce')
                                            ->label('Giới thiệu bản thân')
                                            ->default("Chưa cập nhật"),

                                    ]),
                                ]),
                            Components\ImageEntry::make('profile_photo_path')
                                ->label('Ảnh')
                                ->hiddenLabel()
                                ->getStateUsing(fn ($record) => HelperFunc::generateURLFilePath($record->profile_photo_path))
                                ->grow(false),
                        ])->from('lg'),
                    ]),
                Components\Section::make('Lịch sử dòng tiền')
                    ->schema([
                        Components\ViewEntry::make('transaction_stats')
                            ->view('filament.admin.resources.users.user-transaction-stats')
                            ->columnSpanFull(),
                    ]),

                Components\Section::make('Lịch sử giao dịch')
                    ->schema([
                        Components\RepeatableEntry::make('transactions')
                            ->hiddenLabel()
                            ->schema([
                                Components\Grid::make(5)
                                    ->schema([
                                        Components\TextEntry::make('type_transaction')
                                            ->label('Loại giao dịch')
                                            ->badge()
                                            ->formatStateUsing(fn(string $state): string => match ($state) {
                                                'recharge_point' => 'Nạp tiền',
                                                'bid' => 'Đấu giá',
                                                'buy_product' => 'Mua sản phẩm',
                                                default => 'Khác',
                                            })
                                            ->color(fn(string $state): string => match ($state) {
                                                'recharge_point' => 'success',
                                                'bid' => 'warning',
                                                'buy_product' => 'danger',
                                                default => 'gray',
                                            }),
                                        Components\TextEntry::make('point_change')
                                            ->label('Số dư sau')
                                            ->formatStateUsing(
                                                fn($state) => ($state > 0 ? '+' : '') . number_format($state, 0, ',', '.') . ' ₫'
                                            )
                                            ->color(fn($state): string => $state > 0 ? 'success' : 'danger'),
                                        Components\TextEntry::make('point')
                                            ->label('Số dư hiện tại')
                                            ->formatStateUsing(fn($state) => number_format($state, 0, ',', '.') . ' ₫'),
                                        Components\TextEntry::make('created_at')
                                            ->label('Ngày giao dịch')
                                            ->dateTime(),
                                        Components\TextEntry::make('id')
                                            ->label('Mã giao dịch')
                                            ->prefix('#'),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }


    public function render()
    {
        return view('livewire.filament.customer-info-view');
    }
}
