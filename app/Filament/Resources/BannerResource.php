<?php

namespace App\Filament\Resources;

use App\Enums\Permission\RoleConstant;
use App\Filament\Resources\BannerResource\Pages;
use App\Models\Banner;
use App\Models\BannerType;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Query\Builder;

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole(RoleConstant::ADMIN);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Tên banner')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(1),

                                Forms\Components\Select::make('banner_type_id')
                                    ->label('Loại banner')
                                    ->options(fn() => BannerType::pluck('name', 'id')->toArray())
                                    ->searchable()
                                    ->required()
                                    ->reactive()
                                    ->afterStateHydrated(function ($state, callable $set) {
                                        if ($state) {
                                            $set('banner_type_description', BannerType::find($state)?->description);
                                        }
                                    })
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $set('banner_type_description', BannerType::find($state)?->description);
                                    }),
                            ]),

                        Forms\Components\FileUpload::make('url_image')
                            ->label('Hình ảnh')
                            ->directory('banners')
                            ->image()
                            ->required()
                            ->imageCropAspectRatio('16:9')
                            ->imageResizeMode('cover')
                            ->imagePreviewHeight(220)
                            ->maxSize(5120)
                            ->preserveFilenames(false)
                            ->columnSpan('full'),

                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('link_page')
                                    ->label('Đường dẫn tới trang đích')
                                    ->url()
                                    ->helperText('Nhập URL đầy đủ (ví dụ: https://domain.com/path)')
                                    ->required()
                                    ->columnSpan(1),

                                Forms\Components\Toggle::make('status')
                                    ->label('Kích hoạt')
                                    ->hint('Bật = hiển thị')
                                    ->default(1)
                                    ->inline(false)
                                    ->columnSpan(1),
                            ]),
                    ]),

                Section::make('Danh mục banner')
                    ->description('Chọn loại banner để hiển thị mô tả tương ứng')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Placeholder::make('banner_type_description')
                                    ->label('Mô tả loại banner')
                                    ->content(fn(callable $get) => $get('banner_type_description') ?: 'Chưa có mô tả')
                                    ->columnSpan(2),
                            ]),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\ImageColumn::make('url_image')
                    ->label('Hình ảnh'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Tên banner')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('bannerType.name')
                    ->label('Loại banner'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->formatStateUsing(fn($state) => $state ? 'Kích hoạt' : 'Ẩn'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        1 => 'Kích hoạt',
                        0 => 'Ẩn',
                    ]),
                Tables\Filters\SelectFilter::make('banner_type_id')
                    ->label('Loại banner')
                    ->options(fn() => BannerType::pluck('name', 'id')->toArray())
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBanners::route('/'),
            'create' => Pages\CreateBanner::route('/create'),
            'edit' => Pages\EditBanner::route('/{record}/edit'),
        ];
    }
}
