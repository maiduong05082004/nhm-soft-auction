<?php

namespace App\Filament\Resources;

use App\Enums\Permission\RoleConstant;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Infolists\Components;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationLabel = 'Người dùng';
    protected static ?string $modelLabel = 'Người dùng';
    protected static ?string $pluralModelLabel = 'Người dùng';

    public static function canAccess(): bool
    {
       return auth()->user()->hasRole(RoleConstant::ADMIN);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('email_verified_at'),

                Forms\Components\Fieldset::make('Password')
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->label('Present Password')
                            ->readOnly()
                            ->placeholder('●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●')
                            ->disabled(fn($get,  $context) => $get('showChangePassword') !== true || $context === 'create')
                            ->default(fn($record) => $record?->password ?? '')
                            ->visible(fn($get, $record) => $record !== null && $get('showChangePassword') !== true)
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('changePassword')
                                    ->label('Change password')
                                    ->icon('heroicon-o-pencil')
                                    ->action(function ($get, $set) {
                                        $set('showChangePassword', true);
                                    })
                            ),

                        Forms\Components\TextInput::make('new_password')
                            ->label('New Password')
                            ->password()
                            ->visible(fn($get, $record) => $record === null || $get('showChangePassword') === true)
                            ->required(fn($record) => $record === null)
                            ->dehydrateStateUsing(fn($state) => !empty($state) ? bcrypt($state) : null)
                            ->dehydrated(fn($state) => filled($state))
                            ->maxLength(255),

                        Forms\Components\TextInput::make('new_password_confirmation')
                            ->label('Identity Password')
                            ->password()
                            ->visible(fn($get, $record) => $record === null || $get('showChangePassword') === true)
                            ->same('new_password')
                            ->required(fn($record) => $record === null),

                        Forms\Components\Hidden::make('showChangePassword')->default(false),
                    ]),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->maxLength(255),
                Select::make('role')
                    ->required()
                    ->label('RoleConstant')
                    ->options(function () {
                        if (auth()->user()->role === 'admin') {
                            return [
                                'user' => 'User',
                                'member' => 'Member',
                            ];
                        } else {
                            return [
                                'user' => 'User',
                            ];
                        }
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Tên')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\ImageColumn::make('profile_photo_path')
                    ->label('Ảnh')
                    ->circular()
                    ->defaultImageUrl(fn($record) => $record->profile_photo_url),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Số điện thoại')
                    ->searchable()
                    ->default('no phone'),
                Tables\Columns\TextColumn::make('address')
                    ->label('Địa chỉ')
                    ->sortable(),
                Tables\Columns\TextColumn::make('current_balance')
                    ->formatStateUsing(fn($state) => number_format($state, 0, ',', '.') . ' ₫')
                    ->label('Số dư')
                    ->searchable()
                    ->default(0),
                Tables\Columns\TextColumn::make('membership')
                    ->searchable()
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Membership' : 'Chưa đăng ký')
                    ->badge()
                    ->color(fn(bool $state): string => $state ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('reputation')
                    ->label('Danh tiếng')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Chỉnh sửa'),
                Tables\Actions\Action::make('manager')
                    ->label('Quản lý')
                    ->icon('heroicon-o-user')
                    ->url(fn(User $record) => UserResource::getUrl('view', ['record' => $record])),
                Tables\Actions\Action::make('delete')
                    ->label('Xóa')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->visible(fn() => auth()->user()?->role === 'admin')
                    ->requiresConfirmation()
                    ->modalHeading('Confirm Course Deletion')
                    ->modalDescription('Are you sure you want to delete this course? This action cannot be undone.')
                    ->action(function (User $record) {
                        $record->delete();
                        Notification::make()
                            ->title('Success')
                            ->body('Course deleted successfully!')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make()
                    ->schema([
                        Components\Split::make([
                            Components\Grid::make(2)
                                ->schema([
                                    Components\Group::make([
                                        Components\TextEntry::make('name')->label('Tên người dùng'),
                                        Components\TextEntry::make('email')->label('Email'),
                                        Components\TextEntry::make('phone')->label('Số điện thoại'),
                                    ]),
                                    Components\Group::make([
                                        Components\TextEntry::make('role')->label('Vai trò'),
                                        Components\TextEntry::make('membership')->label('Membership')
                                            ->formatStateUsing(fn(bool $state): string => $state ? 'Membership' : 'Chưa đăng ký')
                                            ->badge()
                                            ->color(fn(bool $state): string => $state ? 'success' : 'danger'),
                                    ]),
                                ]),
                            Components\ImageEntry::make('profile_photo_url')
                                ->label('Ảnh')
                                ->hiddenLabel()
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
                Components\Section::make('Thông tin khác')
                    ->schema([
                        Components\TextEntry::make('created_at')->label('Ngày tạo')->dateTime(),
                        Components\TextEntry::make('updated_at')->label('Ngày cập nhật')->dateTime(),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'view' => Pages\ViewUser::route('/{record}')
        ];
    }
}
