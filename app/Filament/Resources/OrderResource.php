<?php

namespace App\Filament\Resources;

use App\Enums\OrderStatus;
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Filament\Resources\OrderResource\Widgets\OrderStats;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Utils\HelperFunc;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components as Info;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;
use App\Services\Orders\OrderService;
use App\Enums\Permission\RoleConstant;
use Filament\Support\Enums\MaxWidth;

class OrderResource extends Resource
{
    protected static ?string $model = OrderDetail::class;
    protected static ?string $recordTitleAttribute = 'number';
    protected static ?string $modelLabel = 'Chi tiết đơn hàng';
    protected static ?string $navigationLabel = 'Đơn hàng';
    protected static ?string $pluralModelLabel = 'Đơn hàng';
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?int $navigationSort = 1;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole(RoleConstant::ADMIN);
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole(RoleConstant::ADMIN);
    }

    protected static ?OrderService $orderServiceInstance = null;

    protected static function orderService(): OrderService
    {
        return static::$orderServiceInstance ??= app(OrderService::class);
    }

    public static function canEdit(Model $record): bool
    {
        return static::currentUserIsAdmin();
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        return static::currentUserIsAdmin();
    }

    public static function form(Form $form): Form
    {
        $isEdit = $form->getOperation() === 'edit';
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema(static::getDetailsFormSchema())
                            ->columns(2),

                        Forms\Components\Section::make('Sản phẩm trong đơn hàng')
                            // ->headerActions([
                            //     Action::make('Xóa toàn bộ')
                            //         ->modalHeading('Bạn có chắc chắn?')
                            //         ->modalDescription('Tất cả sả phấm sẽ bị xóa khỏi đơn hàng.')
                            //         ->requiresConfirmation()
                            //         ->visible(fn() => !$isEdit && auth()->user()->hasRole(RoleConstant::ADMIN))
                            //         ->color('danger')
                            //         ->action(fn(Forms\Set $set) => $set('items', [])),
                            // ])
                            ->schema([
                                static::getItemsRepeater($isEdit),
                                Forms\Components\Placeholder::make('subtotal_display')
                                    ->label('Tổng tiền sản phẩm')
                                    ->content(function (Forms\Get $get): string {
                                        $items = $get('items') ?? [];
                                        return static::orderService()->formatCurrency(static::orderService()->calculateSubtotal($items));
                                    })
                                    ->columnSpan('full')
                                    ->extraAttributes(['class' => 'text-lg font-bold text-blue-600']),
                                Forms\Components\Placeholder::make('shipping_fee_display')
                                    ->label('Phí vận chuyển')
                                    ->content(function (Forms\Get $get): string {
                                        $shippingFee = (float) ($get('shipping_fee') ?: 0);
                                        return number_format($shippingFee, 0, ',', '.') . ' ₫';
                                    })->columnSpan(['lg' => 1])
                                    ->extraAttributes(['class' => 'text-base text-gray-600']),
                                Forms\Components\Placeholder::make('total_display')
                                    ->label('Tổng tiền đơn hàng')
                                    ->content(function (Forms\Get $get): string {
                                        $items = $get('items') ?? [];
                                        $shippingFee = (float) ($get('shipping_fee') ?: 0);
                                        return static::orderService()->formatCurrency(static::orderService()->calculateTotal($items, $shippingFee));
                                    })->columnSpan(['lg' => 1])
                                    ->extraAttributes(['class' => 'text-lg font-bold text-green-600']),
                                Forms\Components\Placeholder::make('created_at')
                                    ->label('Thời điểm tạo')->columnSpan(['lg' => 1])
                                    ->content(fn(OrderDetail $record): ?string => $record->created_at?->diffForHumans()),

                                Forms\Components\Placeholder::make('updated_at')
                                    ->label('Thời điểm sửa lần cuối')->columnSpan(['lg' => 1])
                                    ->content(fn(OrderDetail $record): ?string => $record->updated_at?->diffForHumans()),
                            ]),
                    ])
                    ->columnSpan(['lg' =>  3]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table->recordUrl(null)
            ->columns([
                Tables\Columns\TextColumn::make('code_orders')
                    ->label('Mã đơn hàng')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Khách hàng')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge(),

                Tables\Columns\TextColumn::make('shipping_fee')
                    ->label('Phí vận chuyển')
                    ->formatStateUsing(fn($state) => number_format($state, 0, ',', '.') . ' ₫')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->formatStateUsing(fn($state) => number_format($state, 0, ',', '.') . ' ₫'),
                    ]),

                Tables\Columns\TextColumn::make('total')
                    ->label('Tổng tiền')
                    ->formatStateUsing(fn($state) => number_format($state, 0, ',', '.') . ' ₫')
                    ->searchable()
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->formatStateUsing(fn($state) => number_format($state, 0, ',', '.') . ' ₫'),
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày đặt hàng')
                    ->date()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TrashedFilter::make(),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('Tạo từ')
                            ->placeholder(fn(): string => 'Dec 18, ' . now()->subYear()->format('Y')),
                        Forms\Components\DatePicker::make('Đến')
                            ->placeholder(fn(): string => now()->format('M d, Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['Tạo từ'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['Đến'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['Tạo từ'] ?? null) {
                            $indicators['Tạo từ'] = 'Order từ ' . Carbon::parse($data['Tạo từ'])->toFormattedDateString();
                        }
                        if ($data['Đến'] ?? null) {
                            $indicators['Đến'] = 'Order đến ' . Carbon::parse($data['Đến'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Chỉnh sửa')
                    ->visible(fn() => static::currentUserIsAdmin()),
                Tables\Actions\ViewAction::make()
                    ->label('Xem')
                    ->modalWidth(MaxWidth::SixExtraLarge),
                Tables\Actions\Action::make('payment_status')
                    ->label('Trạng thái thanh toán')
                    ->icon('heroicon-o-credit-card')
                    ->color('info')
                    ->modalContent(function (OrderDetail $record) {
                        $payment = $record->payments->first();
                        $isAdmin = static::currentUserIsAdmin();

                        return view('filament.admin.resources.orders.payment-status-modal', [
                            'order' => $record,
                            'payment' => $payment,
                            'isAdmin' => $isAdmin
                        ]);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Đóng')
                    ->modalWidth(MaxWidth::SevenExtraLarge),
            ])
            ->groupedBulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->visible(fn() => static::currentUserIsAdmin())
                    ->action(function () {
                        Notification::make()
                            ->title('Now, now, don\'t be cheeky, leave some records for others to play with!')
                            ->warning()
                            ->send();
                    }),
            ])
            ->groups([
                Tables\Grouping\Group::make('created_at')
                    ->label('Order Date')
                    ->date()
                    ->collapsible(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema(static::getInfolistSchema());
    }

    public static function getInfolistSchema(): array
    {
        return [
            Info\Section::make('Thông tin đơn hàng')
                ->schema([
                    Info\Grid::make(2)
                        ->schema([
                            Info\TextEntry::make('code_orders')->label('Mã đơn hàng'),
                            Info\TextEntry::make('user.name')->label('Khách hàng'),
                            Info\TextEntry::make('ship_address')->label('Địa chỉ giao hàng'),
                            Info\TextEntry::make('email_receiver')->label('Email người nhận'),
                            Info\TextEntry::make('status')->label('Trạng thái')->badge(),
                            Info\TextEntry::make('shipping_fee')
                                ->label('Phí vận chuyển')
                                ->formatStateUsing(fn($state) => number_format((float) $state, 0, ',', '.') . ' ₫'),
                            Info\TextEntry::make('subtotal')
                                ->label('Tổng tiền sản phẩm')
                                ->formatStateUsing(fn($state) => number_format((float) $state, 0, ',', '.') . ' ₫'),
                            Info\TextEntry::make('total')
                                ->label('Tổng tiền đơn hàng')
                                ->formatStateUsing(fn($state) => number_format((float) $state, 0, ',', '.') . ' ₫'),
                            Info\TextEntry::make('created_at')->dateTime()->label('Ngày tạo'),
                            Info\TextEntry::make('updated_at')->dateTime()->label('Cập nhật'),
                        ]),
                ]),

            Info\Section::make('Sản phẩm trong đơn hàng')
                ->schema([
                    Info\RepeatableEntry::make('items')
                        ->hiddenLabel()
                        ->schema([
                            Info\Grid::make(5)
                                ->schema([
                                    Info\TextEntry::make('product.name')
                                        ->label('Sản phẩm')
                                        ->columnSpan(2),
                                    Info\TextEntry::make('quantity')->label('Số lượng'),
                                    Info\TextEntry::make('price')
                                        ->label('Đơn giá')
                                        ->state(fn($record) => (float) static::orderService()->getProductCurrentPrice((int) ($record->product_id ?? 0)))
                                        ->formatStateUsing(fn($state) => number_format((float) $state, 0, ',', '.') . ' ₫'),
                                    Info\TextEntry::make('total')
                                        ->label('Tổng tiền')
                                        ->state(function ($record) {
                                            $unit = (float) static::orderService()->getProductCurrentPrice((int) ($record->product_id ?? 0));
                                            $qty = (float) ($record->quantity ?? 0);
                                            return $unit * $qty;
                                        })
                                        ->formatStateUsing(fn($state) => number_format((float) $state, 0, ',', '.') . ' ₫'),
                                ])
                        ])
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),
        ];
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PaymentsRelationManager::class,
        ];
    }

    public static function getWidgets(): array
    {
        return [
            OrderStats::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
            'qr-code' => Pages\QrPayment::route('/{record}/qr-code'),
        ];
    }

    /** @return Builder<Order> */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->withoutGlobalScope(SoftDeletingScope::class);

        $user = auth()->user();

        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        if (!static::currentUserIsAdmin()) {
            $query->where('user_id', $user->id);
        }

        return $query->with(['payments', 'user']);
    }


    public static function getGloballySearchableAttributes(): array
    {
        return ['number', 'customer.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var Order $record */

        return [
            'Customer' => optional($record->customer)->name,
        ];
    }

    /** @return Builder<Order> */
    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['customer', 'items']);
    }

    public static function getNavigationBadge(): ?string
    {

        return (string) static::getEloquentQuery()->count();
    }

    protected static function currentUserIsAdmin(): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }
        if (is_callable([$user, 'hasRole'])) {
            return (bool) call_user_func([$user, 'hasRole'], 'admin');
        }
        return (string) ($user->role ?? '') === 'admin';
    }

    /** @return Forms\Components\Component[] */
    public static function getDetailsFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('code_orders')
                ->label('Mã đơn hàng')
                ->default('ORD' . HelperFunc::getTimestampAsId())
                ->disabled()
                ->dehydrated()
                ->required()
                ->maxLength(32)
                ->unique(OrderDetail::class, 'code_orders', ignoreRecord: true),

            Forms\Components\Select::make('user_id')
                ->label('Khách hàng')
                ->relationship('user', 'name')
                ->getOptionLabelFromRecordUsing(fn(Model $record) => "{$record->name} - {$record->phone}")
                ->searchable()
                ->required()
                ->live()
                ->afterStateUpdated(function ($state, Forms\Set $set) {
                    if ($state) {
                        $user = \App\Models\User::find($state);
                        if ($user && $user->address) {
                            $set('ship_address', $user->address);
                            $set('email_receiver', $user->email);
                        }
                    }
                })
                ->createOptionForm([
                    Forms\Components\TextInput::make('name')
                        ->label('Tên khách hàng')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->required()
                        ->email()
                        ->maxLength(255)
                        ->unique(),

                    Forms\Components\TextInput::make('phone')
                        ->label('Số điện thoại')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('address')
                        ->label('Địa chỉ')
                        ->required()
                        ->maxLength(255),

                ])
                ->createOptionAction(function (Action $action) {
                    return $action
                        ->modalHeading('Tạo khách hàng')
                        ->modalSubmitActionLabel('Tạo khách hàng')
                        ->modalWidth('lg');
                }),

            Forms\Components\Select::make('ship_address')
                ->label('Địa chỉ giao hàng')
                ->options(function (Forms\Get $get) {
                    $userId = $get('user_id');
                    if (!$userId) {
                        return [];
                    }

                    $user = \App\Models\User::find($userId);
                    if (!$user || !$user->address) {
                        return [];
                    }

                    return [$user->address => $user->address];
                })
                ->searchable()
                ->required()
                ->visible(fn(Forms\Get $get) => !empty($get('user_id')))
                ->placeholder('Chọn khách hàng trước')
                ->suffixAction(
                    Forms\Components\Actions\Action::make('editAddress')
                        ->label('Sửa địa chỉ')
                        ->icon('heroicon-o-pencil')
                        ->color('warning')
                        ->modalHeading('Sửa địa chỉ giao hàng')
                        ->modalDescription('Nhập địa chỉ giao hàng mới cho đơn hàng này')
                        ->modalSubmitActionLabel('Cập nhật')
                        ->modalCancelActionLabel('Hủy')
                        ->form([
                            Forms\Components\Textarea::make('new_address')
                                ->label('Địa chỉ mới')
                                ->required()
                                ->rows(3)
                                ->placeholder('Nhập địa chỉ giao hàng...')
                                ->default(fn(Forms\Get $get) => $get('ship_address'))
                        ])
                        ->action(function (array $data, Forms\Set $set, Forms\Get $get) {
                            $set('ship_address', $data['new_address']);

                            $userId = $get('user_id');
                            if ($userId) {
                                $user = \App\Models\User::find($userId);
                                if ($user) {
                                    $user->update(['address' => $data['new_address']]);
                                }
                            }

                            \Filament\Notifications\Notification::make()
                                ->title('Thành công!')
                                ->body('Địa chỉ giao hàng đã được cập nhật')
                                ->success()
                                ->send();
                        })
                        ->visible(fn(Forms\Get $get) => !empty($get('user_id')))
                ),

            Forms\Components\ToggleButtons::make('status')
                ->inline()
                ->label('Trạng thái')
                ->options(OrderStatus::class)
                ->required(),

            Forms\Components\TextInput::make('email_receiver')
                ->label('Email người nhận')
                ->maxLength(255)
                ->required()
                ->live()
                ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                    if (empty($state)) {
                        $userId = $get('user_id');
                        if ($userId) {
                            $user = \App\Models\User::find($userId);
                            if ($user && $user->email) {
                                $set('email_receiver', $user->email);
                            }
                        }
                    }
                })
                ->suffixAction(
                    Forms\Components\Actions\Action::make('useUserEmail')
                        ->label('Dùng email người dùng')
                        ->icon('heroicon-o-user')
                        ->color('info')
                        ->tooltip('Sử dụng email của khách hàng đã chọn')
                        ->action(function (Forms\Set $set, Forms\Get $get) {
                            $userId = $get('user_id');
                            if ($userId) {
                                $user = \App\Models\User::find($userId);
                                if ($user && $user->email) {
                                    $set('email_receiver', $user->email);

                                    \Filament\Notifications\Notification::make()
                                        ->title('Thành công!')
                                        ->body('Đã sử dụng email của khách hàng: ' . $user->email)
                                        ->success()
                                        ->send();
                                } else {
                                    \Filament\Notifications\Notification::make()
                                        ->title('Lỗi!')
                                        ->body('Khách hàng này không có email')
                                        ->danger()
                                        ->send();
                                }
                            } else {
                                \Filament\Notifications\Notification::make()
                                    ->title('Lỗi!')
                                    ->body('Vui lòng chọn khách hàng trước')
                                    ->warning()
                                    ->send();
                            }
                        })
                        ->visible(fn(Forms\Get $get) => !empty($get('user_id')))
                )
                ->placeholder('Nhập email hoặc chọn email từ khách hàng')
                ->columnSpan('full'),

            Forms\Components\Hidden::make('subtotal')
                ->default(0)
                ->dehydrated()
                ->required(),

            Forms\Components\Hidden::make('total')
                ->default(0)
                ->dehydrated()
                ->required(),



            Forms\Components\MarkdownEditor::make('note')
                ->label('Ghi chú')
                ->columnSpan('full'),
        ];
    }

    public static function getItemsRepeater(bool $isEdit = false): Repeater
    {
        return Repeater::make('items')
            ->label('Sản phẩm')
            ->relationship()
            ->addable(! $isEdit)
            ->deletable(! $isEdit)
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label('Sản phẩm')
                    ->options(Product::query()->pluck('name', 'id'))
                    ->required()
                    ->disabled($isEdit)
                    ->reactive()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        if ($state) {
                            $price = static::orderService()->getProductCurrentPrice($state);
                            $set('price', $price);
                            $set('quantity', 1);
                            $set('subtotal', $price * 1);
                        }
                    })
                    ->columnSpan([
                        'md' => 6,
                    ])
                    ->searchable(),

                Forms\Components\TextInput::make('quantity')
                    ->label('Số lượng')
                    ->numeric()
                    ->default(1)
                    ->disabled($isEdit)
                    ->minValue(1)
                    ->live(debounce: 700)
                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                        $quantity = (float) ($state ?: 1);
                        $price = (float) ($get('price') ?: 0);
                        $line = $quantity * $price;
                        $set('subtotal', $line);
                    })
                    ->columnSpan([
                        'md' => 2,
                    ])
                    ->afterStateHydrated(function (Forms\Set $set, Forms\Get $get) {
                        $productId = $get('product_id');
                        if ($productId) {
                            $price = static::orderService()->getProductCurrentPrice($productId);
                            $quantity = (float) ($get('quantity') ?: 0);
                            $set('price', $price);
                            $set('subtotal', $price * $quantity);
                        }
                    })
                    ->required(),

                Forms\Components\TextInput::make('price')
                    ->label('Đơn giá')
                    ->disabled()
                    ->dehydrated()
                    ->numeric()
                    ->required()
                    ->columnSpan([
                        'md' => 2,
                    ]),

                Forms\Components\TextInput::make('subtotal')
                    ->label('Thành tiền')
                    ->disabled()
                    ->dehydrated()
                    ->numeric()
                    ->default(0)
                    ->columnSpan([
                        'md' => 2,
                    ]),

                Forms\Components\Hidden::make('total')
                    ->label('Tổng tiền')
                    ->disabled()
                    ->dehydrated()
                    ->default(0)
                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                        $subtotal = (float) ($get('subtotal') ?: 0);
                        $shippingFee = (float) ($get('shipping_fee') ?: 0);
                        $set('total', $subtotal + $shippingFee);
                    })
                    ->columnSpan([
                        'md' => 2,
                    ]),
            ])
            ->columns(12)
            ->defaultItems(1)
            ->reorderable(false)
            ->collapsible(false)
            ->addActionLabel('Thêm sản phẩm')
            ->required();
    }

    public static function getPaymentFormSchema(): array
    {
        return [
            Forms\Components\Select::make('payment_method')
                ->label('Phương thức thanh toán')
                ->options([
                    '0' => 'Giao dịch trực tiếp',
                    '1' => 'Chuyển khoản ngân hàng',
                ])
                ->required()
                ->default('0'),

            Forms\Components\TextInput::make('shipping_fee')
                ->label('Phí vận chuyển')
                ->numeric()
                ->default(0)
                ->required(),

            Forms\Components\Placeholder::make('subtotal')
                ->label('Tổng tiền sản phẩm')
                ->content(function (Forms\Get $get): string {
                    $items = $get('items') ?? [];
                    $subtotal = 0;

                    foreach ($items as $item) {
                        if (isset($item['quantity']) && isset($item['price'])) {
                            $subtotal += $item['quantity'] * $item['price'];
                        }
                    }

                    return number_format($subtotal, 0, ',', '.') . ' ₫';
                }),

            Forms\Components\Placeholder::make('total')
                ->label('Tổng tiền đơn hàng')
                ->content(function (Forms\Get $get): string {
                    $items = $get('items') ?? [];
                    $subtotal = 0;
                    $shippingFee = $get('shipping_fee') ?: 0;

                    foreach ($items as $item) {
                        if (isset($item['quantity']) && isset($item['price'])) {
                            $subtotal += $item['quantity'] * $item['price'];
                        }
                    }

                    $total = $subtotal + $shippingFee;
                    return number_format($total, 0, ',', '.') . ' ₫';
                })
                ->extraAttributes(['class' => 'text-lg font-bold text-green-600']),
        ];
    }
}
