<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PointPackageResource\Pages;
use App\Models\PointPackage;
use Filament\Forms\Components\TextInput;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use App\Enums\Permission\RoleConstant;
use Filament\Tables;
use Filament\Tables\Table;
use FilamentTiptapEditor\Enums\TiptapOutput;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Database\Eloquent\Builder;

class PointPackageResource extends Resource
{
    protected static ?string $model = PointPackage::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $label = "gói nạp điểm";

    protected static ?string $pluralLabel = "gói nạp điểm";

    protected static ?string $navigationLabel = "Gói nạp điểm";

    public static function getNavigationUrl(): string
    {
        if (auth()->check() && auth()->user()->hasRole(RoleConstant::CUSTOMER)) {
            return static::getUrl('buy');
        }

        return static::getUrl('index');
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Tên gói')
                            ->required()
                            ->maxLength(255),

                        TiptapEditor::make('description')
                            ->label('Mô tả')
                            ->output(TiptapOutput::Html)
                            ->extraInputAttributes(['style' => 'min-height: 250px;'])
                            ->columnSpanFull()
                            ->nullable(),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('points')
                                    ->label('Số điểm')
                                    ->numeric()
                                    ->minValue(1)
                                    ->required()
                                    ->helperText('Số điểm người dùng nhận được khi mua gói này.'),

                                TextInput::make('discount')
                                    ->label('Giảm (%)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->default(0)
                                    ->required()
                                    ->helperText('Phần trăm khuyến mãi, 0–100.'),
                            ]),

                        Forms\Components\Toggle::make('status')
                            ->label('Kích hoạt')
                            ->default(true)
                            ->helperText('Bật để gói hiển thị cho người dùng.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Tên')->searchable()->sortable()->limit(30),
                Tables\Columns\TextColumn::make('points')->label('Điểm')->sortable(),
                Tables\Columns\TextColumn::make('discount')
                    ->label('Giảm (%)')
                    ->formatStateUsing(fn($state) => "{$state}%")
                    ->sortable(),
                Tables\Columns\IconColumn::make('status')
                    ->label('Trạng thái')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Tạo lúc')->sortable()->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        1 => 'Hoạt động',
                        0 => 'Không hoạt động',
                    ]),
                Tables\Filters\Filter::make('min_points')
                    ->form([
                        TextInput::make('points')->label('Tối thiểu điểm')->numeric(),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query->when($data['points'] ?? null, fn($q, $v) => $q->where('points', '>=', $v));
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListPointPackages::route('/'),
            'create' => Pages\CreatePointPackage::route('/create'),
            'edit' => Pages\EditPointPackage::route('/{record}/edit'),
            'buy' => Pages\BuyPointPackage::route('/buy')
        ];
    }
}
