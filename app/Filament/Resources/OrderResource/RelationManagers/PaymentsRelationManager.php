<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $recordTitleAttribute = 'reference';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('order_detail_id')
                    ->label('Mã đơn hàng')
                    ->disabled()
                    ->columnSpan('full')
                    ->required(),

                Forms\Components\TextInput::make('amount')
                    ->label('Số tiền thanh toán')
                    ->numeric()
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
            ->heading('Thanh toán')
            ->columns([
                Tables\Columns\TextColumn::make('orderDetail.code_orders')
                    ->label('Mã đơn hàng')
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Số tiền thanh toán')
                    ->sortable()
                    ->formatStateUsing(fn($state) => number_format($state, 0, ',', '.') . ' ₫'),

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
                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->payment_method == '0') {
                            return $state == 'success' ? 'Giao dịch trực tiếp' : 'Chưa nhận tiền';
                        }
                        if ($record->payment_method == '1') {
                            return $state == 'success' ? 'Đã thanh toán' : 'Chưa thanh toán';
                        }
                        return 'Không xác định';
                    })
                    ->sortable(),
            ])
            ->filters([
                //
            ]);
    }
}
