<?php

namespace App\Livewire\Filament;

use App\Enums\ImageStoragePath;
use App\Services\Auth\AuthServiceInterface;
use App\Utils\HelperFunc;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Filament\Forms;

class CustomerInfoEdit extends Component implements HasForms
{
    use InteractsWithForms;

    public ?Authenticatable $auth;

    public ?array $data = [];

    public function boot(AuthServiceInterface $service)
    {
        $this->service = $service;
    }

    public function mount(): void
    {
        $data = $this->service->getInfoAuth();
        $this->form->fill([
            'profile_photo_url' => $data->profile_photo_url,
            'name' => $data->name,
            'email' => $data->email,
            'phone' => $data->phone,
            'address' => $data->address,
            'introduce' => $data->introduce,
            'new_password' => '',
            'new_password_confirmation' => '',
            'bin_bank' => $data->creditCards?->first()?->bin_bank ?? null,
            'card_number' => $data->creditCards?->first()?->card_number ?? null,
            'card_holder_name' => $data->creditCards?->first()?->name ?? null,
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->fill()
            ->schema([


                Forms\Components\Fieldset::make('identification')
                    ->label("Thông tin cá nhân")
                    ->schema([
                        Forms\Components\FileUpload::make('profile_photo_url')
                            ->label('Ảnh đại diện')
                            ->avatar()
                            ->imageEditor()
                            ->storeFiles(false)
                            ->preserveFilenames()
                            ->reorderable()
                            ->columnSpanFull()
                            ->alignCenter()
                            ->helperText("Ảnh đại diện sẽ được hiển thị trên trang cá nhân của bạn. Vui lòng chọn ảnh có kích thước tối ưu để hiển thị tốt nhất.")
                            ->maxSize(5120)  // 5MB = 5 * 1024 KB
                            ,
                        Forms\Components\TextInput::make('name')
                            ->label('Tên')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->readonly()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('Số điện thoại')
                            ->placeholder("Ví dụ: 0987654321")
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('address')
                            ->label('Địa chỉ')
                            ->placeholder("Ví dụ: 123 Đường ABC, Phường 1, TP.Hà Nội")
                            ->helperText("Địa chỉ của bạn cũng sẽ được sử dụng để giao hàng hoặc là nơi lấy sản phẩm. Vui lòng cung cấp địa chỉ chính xác.")
                            ->columnSpanFull()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('introduce')
                            ->label('Giới thiệu bản thân')
                            ->placeholder("Ví dụ: Tôi là một người yêu thích công nghệ, thích khám phá những điều mới mẻ trong cuộc sống.")
                            ->columnSpanFull()
                            ->maxLength(255),
                    ]),

                Forms\Components\Fieldset::make('Password')
                    ->label("Đổi Mật khẩu")
                    ->schema([
                        Forms\Components\TextInput::make('new_password')
                            ->label('Mật khẩu mới')
                            ->required(fn($record) => !empty($record))
                            ->password()
                            ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                            ->dehydrated(fn($state) => filled($state))
                            ->minLength(8)
                            ->validationMessages([
                                'minLength' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
                            ]),

                        Forms\Components\TextInput::make('new_password_confirmation')
                            ->label('Xác nhận mật khẩu mới')
                            ->password()
                            ->same('new_password')
                            ->validationMessages([
                                'same' => 'Mật khẩu xác nhận không khớp với mật khẩu mới.',
                            ]),
                    ]),

                Forms\Components\Fieldset::make('payment')
                    ->label("Thông tin tài khoản ngân hàng")
                    ->schema([
                        Forms\Components\Select::make('bin_bank')
                            ->label('Ngân hàng')
                            ->searchable()
                            ->required(fn (callable $get) => $get('card_number') || $get('card_holder_name'))
                            ->options(HelperFunc::getListBankOptions()),
                        Forms\Components\TextInput::make('card_number')
                            ->label('Số tài khoản ngân hàng')
                            ->numeric()
                            ->required(fn (callable $get) => $get('bin_bank') || $get('card_holder_name')),
                        Forms\Components\TextInput::make('card_holder_name')
                            ->label('Chủ tài khoản')
                            ->required(fn (callable $get) => $get('bin_bank') || $get('card_number'))
                            ->reactive()
                            ->debounce(1000)
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    // 1. Loại bỏ dấu
                                    $state = HelperFunc::removeVietnameseTones($state);
                                    $newState = strtoupper($state);
                                    // Cập nhật giá trị trên FE ngay lập tức
                                    $set('card_holder_name', $newState);
                                }
                            })
                            ->dehydrateStateUsing(function (?string $state) {
                                if (!$state) return null;
                                $state = HelperFunc::removeVietnameseTones($state);
                                return strtoupper($state);
                            }),
                    ]),

            ])->statePath('data');
    }

    public function create(): void
    {
        $form = $this->form->getState();
        $result = $this->service->updateAuthUser($form);
        if ($result){
            Notification::make()
                ->title('Thành công')
                ->body('Cập nhật thông tin thành công!')
                ->success()
                ->send();
        }else{
            Notification::make()
                ->title('Thất bại')
                ->body('Cập nhật thông tin thất bại!')
                ->danger()
                ->send();
        }
    }

    public function render()
    {
        return view('livewire.filament.customer-info-edit');
    }
}
