<?php

namespace App\Filament\Pages\Auth;

use App\Enums\Permission\RoleConstant;
use App\Models\User;
use App\Notifications\RegisterEmailVerification;
use App\Responses\OverrideRegistrationResponse;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Events\Auth\Registered;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Register as BaseRegister;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class Register extends BaseRegister
{

    public function register(): ?OverrideRegistrationResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();
            return null;
        }

        $user = $this->wrapInDatabaseTransaction(function () {
            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeRegister($data);

            $this->callHook('beforeRegister');

            $user = $this->handleRegistration($data);

            $this->form->model($user)->saveRelationships();

            $this->callHook('afterRegister');

            return $user;
        });

        event(new Registered($user));

        $this->sendEmailVerificationNotification($user);

        return app(OverrideRegistrationResponse::class);
    }

    protected function handleRegistration(array $data): Model
    {
        /**
         * @var User $user
         */
        $user = $this->getUserModel()::create($data);
        // gán quyền
        $user->assignRole(RoleConstant::CUSTOMER);
        return $user;
    }

    protected function sendEmailVerificationNotification(Model $user): void
    {
        if (!$user instanceof MustVerifyEmail) {
            return;
        }
        if ($user->hasVerifiedEmail()) {
            return;
        }
        $url = route('verify', ['id' => $user->getKey(), 'hash' => sha1($user->getEmailForVerification())]);
        $user->notify(new RegisterEmailVerification($url));
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function getNameFormComponent(): Component
    {
        return TextInput::make('name')
            ->label(__('filament-panels::pages/auth/register.form.name.label'))
            ->required()
            ->maxLength(255)
            ->autofocus()
            ->validationMessages([
                'required' => __('Trường này là bắt buộc'),
                'max' => __('Tên không được vượt quá :max ký tự'),
            ]);
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label(__('filament-panels::pages/auth/register.form.email.label'))
            ->email()
            ->required()
            ->maxLength(255)
            ->unique($this->getUserModel());
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label(__('filament-panels::pages/auth/register.form.password.label'))
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->rule(Password::default())
            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
            ->same('passwordConfirmation')
            ->validationMessages([
                'required' => __('Mật khẩu là bắt buộc.'),
                'min' => __('Mật khẩu phải có ít nhất :min ký tự.'),
                'same' => __('Mật khẩu xác nhận không khớp.'),
            ]);
    }

    protected function getPasswordConfirmationFormComponent(): Component
    {
        return TextInput::make('passwordConfirmation')
            ->label(__('filament-panels::pages/auth/register.form.password_confirmation.label'))
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->dehydrated(false);
    }


}
