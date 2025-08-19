<?php

namespace App\Filament\Resources;

use App\Enums\Permission\RoleConstant;
use App\Filament\Resources\MembershipPlansResource\Pages;
use App\Models\MembershipPlan;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Forms\Components\ColorPicker;

class MembershipPlansResource extends Resource
{
    protected static ?string $model = MembershipPlan::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Gói thành viên';

    protected static ?string $modelLabel = 'Gói thành viên';

    protected static ?string $pluralModelLabel = 'Gói thành viên';

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole(RoleConstant::ADMIN);
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Tên gói thành viên')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull()
                    ->live(onBlur: true),

                Forms\Components\TextInput::make('price')
                    ->label('Số điểm yêu cầu')
                    ->required()
                    ->numeric()
                    ->minValue(0),

                Forms\Components\TextInput::make('duration')
                    ->label('Thời gian')
                    ->required()
                    ->numeric()
                    ->placeholder('Bao nhiêu tháng')
                    ->minValue(0),

                Forms\Components\Select::make('status')
                    ->label('Trạng thái')
                    ->options([
                        true => 'Hoạt động',
                        false => 'Không hoạt động',
                    ])
                    ->default(true)
                    ->required(),
                Forms\Components\TextInput::make('sort')
                    ->label('Sắp xếp')
                    ->helperText("Số càng nhỏ, gói sẽ hiển thị càng cao trong danh sách")
                    ->integer()
                    ->minValue(0),
                Forms\Components\TextInput::make('badge')
                    ->label('Huy hiệu gói thành viên')
                    ->maxLength(255),

                ColorPicker::make('badge_color')
                    ->label('Chọn màu huy hiệu')
                    ->default('#ff5733'),

                Forms\Components\Textarea::make('description')
                    ->label('Miêu tả')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255),
                Forms\Components\Section::make('Cấu hình quyền lợi')
                    ->schema([
                        Forms\Components\Toggle::make('config.free_product_listing')
                            ->label('Đăng sản phẩm miễn phí')
                            ->helperText('Cho phép đăng sản phẩm không mất phí')
                            ->default(false),

                        Forms\Components\Toggle::make('config.free_auction_participation')
                            ->label('Tham gia đấu giá miễn phí')
                            ->helperText('Cho phép tham gia đấu giá không mất phí')
                            ->default(false),

                        Forms\Components\Toggle::make('config.priority_support')
                            ->label('Hỗ trợ ưu tiên')
                            ->helperText('Được ưu tiên hỗ trợ khi có vấn đề')
                            ->default(false),

                        Forms\Components\Toggle::make('config.featured_listing')
                            ->label('Sản phẩm nổi bật')
                            ->helperText('Sản phẩm được hiển thị ở vị trí nổi bật')
                            ->default(false),

                        Forms\Components\TextInput::make('config.discount_percentage')
                            ->label('Phần trăm giảm giá (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%')
                            ->default(0)
                            ->helperText('Giảm giá khi mua sản phẩm'),

                        Forms\Components\TextInput::make('config.max_products_per_month')
                            ->label('Số sản phẩm tối đa/tháng')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->helperText('0 = không giới hạn'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Tên gói')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Giá')
                    ->suffix('₫')
                    ->sortable(),

                Tables\Columns\TextColumn::make('duration')
                    ->label('Thời gian')
                    ->suffix(' tháng')
                    ->sortable(),

                Tables\Columns\IconColumn::make('config.free_product_listing')
                    ->label('Đăng SP miễn phí')
                    ->boolean()
                    ->trueIcon('heroicon-o-check')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('success')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('config.discount_percentage')
                    ->label('Giảm giá')
                    ->suffix('%')
                    ->default('0%'),

                Tables\Columns\IconColumn::make('status')
                    ->label('Trạng thái')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        true => 'Hoạt động',
                        false => 'Không hoạt động',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Xem'),
                Tables\Actions\EditAction::make()->label('Sửa'),
                Tables\Actions\DeleteAction::make()->label('Xóa'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Xóa nhiều'),
                ]),
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
            'index' => Pages\ListMembershipPlans::route('/'),
            'create' => Pages\CreateMembershipPlans::route('/create'),
            'view' => Pages\ViewMembershipPlans::route('/{record}'),
            'edit' => Pages\EditMembershipPlans::route('/{record}/edit'),
        ];
    }
}
