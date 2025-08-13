<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Akaunting\Money\Currency;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $recordTitleAttribute = 'reference';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('order_id')
                    ->label('Mã thanh toán')
                    ->disabled()
                    ->columnSpan('full')
                    ->required(),

                Forms\Components\TextInput::make('amount')
                    ->label('Số tiền thanh toán')
                    ->numeric()
                    ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                    ->required(),

                Forms\Components\Select::make('payment_method')
                    ->label('Phương thức thanh toán')
                    ->options([
                        '0' => 'Giao dịch trực tiếp',
                        '1' => 'Chuyển khoản ngân hàng',
                    ])
                    ->required()
                    ->default('0'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ColumnGroup::make('Details')
                    ->columns([
                        Tables\Columns\TextColumn::make('order_id')
                            ->label('Mã đơn hàng')
                            ->searchable(),

                        Tables\Columns\TextColumn::make('amount')
                            ->label('Số tiền thanh toán')
                            ->sortable()
                            ->formatStateUsing(fn($state) => number_format($state, 0, ',', '.') . ' ₫'),
                    ]),

                Tables\Columns\ColumnGroup::make('Context')
                    ->columns([
                        Tables\Columns\TextColumn::make('payment_method')
                            ->label('Phương thức thanh toán')
                            ->formatStateUsing(function ($state) {
                                if ($state == '1') {
                                    return 'Chuyển khoản ngân hàng';
                                } else {
                                    return 'Giao dịch trực tiếp';
                                }
                            })
                            ->sortable(),
                    ]),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->groupedBulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
