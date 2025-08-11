<?php

namespace App\Filament\Resources;

use App\Enums\OrderStatus;
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Filament\Resources\OrderResource\Widgets\OrderStats;
use App\Forms\Components\AddressForm;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $recordTitleAttribute = 'number';
    protected static ?string $navigationLabel = 'Đơn hàng';
    protected static ?string $pluralModelLabel = 'Đơn hàng';
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema(static::getDetailsFormSchema())
                            ->columns(2),

                        Forms\Components\Section::make('Order items')
                            ->headerActions([
                                Action::make('reset')
                                    ->modalHeading('Are you sure?')
                                    ->modalDescription('All existing items will be removed from the order.')
                                    ->requiresConfirmation()
                                    ->color('danger')
                                    ->action(fn(Forms\Set $set) => $set('items', [])),
                            ])
                            ->schema([
                                static::getItemsRepeater(),
                            ]),
                    ])
                    ->columnSpan(['lg' => fn(?Order $record) => $record === null ? 3 : 2]),

                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Created at')
                            ->content(fn(Order $record): ?string => $record->created_at?->diffForHumans()),

                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Last modified at')
                            ->content(fn(Order $record): ?string => $record->updated_at?->diffForHumans()),
                    ])
                    ->columnSpan(['lg' => 1])
                    ->hidden(fn(?Order $record) => $record === null),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
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

                Tables\Columns\TextColumn::make('total')
                    ->label('Tổng tiền')
                    ->formatStateUsing(fn($state) => number_format($state, 0, ',', '.') . ' ₫')
                    ->searchable()
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money(),
                    ]),
                Tables\Columns\TextColumn::make('shipping_fee')
                    ->label('Phí vận chuyển')
                    ->formatStateUsing(fn($state) => number_format($state, 0, ',', '.') . ' ₫')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money(),
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày đặt hàng')
                    ->date()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->placeholder(fn($state): string => 'Dec 18, ' . now()->subYear()->format('Y')),
                        Forms\Components\DatePicker::make('created_until')
                            ->placeholder(fn($state): string => now()->format('M d, Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Order from ' . Carbon::parse($data['created_from'])->toFormattedDateString();
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Order until ' . Carbon::parse($data['created_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Chỉnh sửa'),
                Tables\Actions\ViewAction::make()
                    ->label('Xem'),
            ])
            ->groupedBulkActions([
                Tables\Actions\DeleteBulkAction::make()
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
        ];
    }

    /** @return Builder<Order> */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScope(SoftDeletingScope::class);
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
        /** @var class-string<Model> $modelClass */
        $modelClass = static::$model;

        return (string) $modelClass::where('status', 'new')->count();
    }

    /** @return Forms\Components\Component[] */
    public static function getDetailsFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('code_orders')
                ->label('Mã đơn hàng')
                ->default('ORD-' . random_int(100000, 999999))
                ->disabled()
                ->dehydrated()
                ->required()
                ->maxLength(32)
                ->unique(Order::class, 'code_orders', ignoreRecord: true),

            Forms\Components\Select::make('user_id')
                ->label('Khách hàng')
                ->relationship('user', 'name')
                ->searchable()
                ->required()
                ->live()
                ->afterStateUpdated(function ($state, Forms\Set $set) {
                    if ($state) {
                        $user = \App\Models\User::find($state);
                        if ($user && $user->address) {
                            $set('ship_address', $user->address);
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
                ->visible(fn (Forms\Get $get) => !empty($get('user_id')))
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
                                ->default(fn (Forms\Get $get) => $get('ship_address'))
                        ])
                        ->action(function (array $data, Forms\Set $set) {
                            $set('ship_address', $data['new_address']);
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Thành công!')
                                ->body('Địa chỉ giao hàng đã được cập nhật')
                                ->success()
                                ->send();
                        })
                        ->visible(fn (Forms\Get $get) => !empty($get('ship_address')))
                ),

            Forms\Components\ToggleButtons::make('status')
                ->inline()
                ->options(OrderStatus::class)
                ->required(),



            AddressForm::make('address')
                ->columnSpan('full'),

            Forms\Components\MarkdownEditor::make('notes')
                ->columnSpan('full'),
        ];
    }

    public static function getItemsRepeater(): Repeater
    {
        return Repeater::make('items')
            ->relationship()
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label('Sản phẩm')
                    ->options(Product::query()->pluck('name', 'id'))
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn($state, Forms\Set $set) => $set('price', Product::find($state)?->price ?? 0))
                    ->distinct()
                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                    ->columnSpan([
                        'md' => 5,
                    ])
                    ->searchable(),

                Forms\Components\TextInput::make('qty')
                    ->label('Số lượng')
                    ->numeric()
                    ->default(1)
                    ->columnSpan([
                        'md' => 2,
                    ])
                    ->required(),

                Forms\Components\TextInput::make('price')
                    ->label('Đơn giá')
                    ->disabled()
                    ->dehydrated()
                    ->numeric()
                    ->required()
                    ->columnSpan([
                        'md' => 3,
                    ]),
            ])
            ->required();
    }
}
