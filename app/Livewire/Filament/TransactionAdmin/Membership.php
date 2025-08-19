<?php

namespace App\Livewire\Filament\TransactionAdmin;

use App\Enums\Membership\MembershipTransactionStatus;
use App\Services\Transaction\TransactionServiceInterface;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Livewire\Component;

class Membership extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    private TransactionServiceInterface $service;

    public function boot(TransactionServiceInterface $service)
    {
        $this->service = $service;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->service->getQueryTransactionMembershipAdmin())
            ->columns([
                TextColumn::make('transaction_code')
                    ->label('Mã giao dịch')
                    ->copyable()
                    ->tooltip("Nhấn để sao chép mã giao dịch")
                    ->copyMessage('Copy mã giao dich thành công')
                    ->searchable(),
                TextColumn::make('user.email')
                    ->description(fn ($record) => $record->user->name)
                    ->label('Người dùng')
                    ->searchable(),
                TextColumn::make('membershipUser.membershipPlan.name')
                    ->label('Gói Membership đăng ký')
                    ->searchable(),
                TextColumn::make('money')
                    ->label('Số tiền')
                    ->money('vnd'),
                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->formatStateUsing(fn(string $state) => MembershipTransactionStatus::getLabel((int)$state))
                    ->color(fn(string $state): string => match (MembershipTransactionStatus::from((int)$state)) {
                        MembershipTransactionStatus::WAITING => 'warning',
                        MembershipTransactionStatus::ACTIVE => 'success',
                        MembershipTransactionStatus::FAILED => 'danger',
                        default => 'default',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        MembershipTransactionStatus::WAITING->value => MembershipTransactionStatus::getLabel(MembershipTransactionStatus::WAITING->value),
                        MembershipTransactionStatus::ACTIVE->value => MembershipTransactionStatus::getLabel(MembershipTransactionStatus::ACTIVE->value),
                        MembershipTransactionStatus::FAILED->value => MembershipTransactionStatus::getLabel(MembershipTransactionStatus::FAILED->value),
                    ]),
            ])
            ->actions([
                Action::make('change_status_success')
                    ->label('Xác nhận')
                    ->visible(fn ($record) => in_array($record->status, [MembershipTransactionStatus::WAITING->value, MembershipTransactionStatus::FAILED->value]))
                    ->action(function ($record) {
                        $result = $this->service->confirmMembershipTransaction($record, MembershipTransactionStatus::ACTIVE);
                        if ($result){
                            Notification::make()
                                ->title('Thành công')
                                ->body('Xác nhận giao dịch thành công')
                                ->success()
                                ->send();
                        }else{
                            Notification::make()
                                ->title('Thất bại')
                                ->body('Xác nhận giao dịch thất bại')
                                ->danger()
                                ->send();
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Xác nhận giao dịch')
                    ->modalDescription('Bạn có chắc chắn muốn thực hiện hành động này?')
                    ->modalSubmitActionLabel('Xác nhận')
                    ->icon('heroicon-o-check')
                    ->color('success'),
                Action::make('change_status_failed')
                    ->label('Hủy bỏ')
                    ->visible(fn ($record) => $record->status == MembershipTransactionStatus::WAITING->value)
                    ->action(function ($record) {
                        $result = $this->service->confirmMembershipTransaction($record, MembershipTransactionStatus::FAILED);
                        if ($result){
                            Notification::make()
                                ->title('Thành công')
                                ->body('Hủy bỏ giao dịch thành công')
                                ->success()
                                ->send();
                        }else{
                            Notification::make()
                                ->title('Thất bại')
                                ->body('Hủy bỏ giao dịch thất bại')
                                ->danger()
                                ->send();
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Hủy bỏ giao dịch')
                    ->modalDescription('Bạn có chắc chắn muốn thực hiện hành động này?')
                    ->modalSubmitActionLabel('Xác nhận')
                    ->icon('heroicon-o-exclamation-circle')
                    ->color('danger'),
            ])
            ->emptyStateHeading("Chưa có giao dịch nào")
            ->emptyStateIcon("heroicon-o-rectangle-stack")
            ->emptyStateDescription("Hiện tại chưa có giao dịch nào được thực hiện. Vui lòng quay lại sau.")
            ->defaultPaginationPageOption(25)
            ->defaultSort('created_at', 'desc');
    }

    public function render()
    {
        return view('livewire.filament.transaction-admin.membership');
    }
}
