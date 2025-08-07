<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                    ->label('Role')
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
                Forms\Components\TextInput::make('referral_link')
                    ->maxLength(255),
                Forms\Components\TextInput::make('ref_by')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\ImageColumn::make('profile_photo_path')
                    ->label('Avatar')
                    ->circular()
                    ->defaultImageUrl(fn($record) => $record->profile_photo_url),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->default('no phone'),
                Tables\Columns\TextColumn::make('address')
                    ->sortable(),
                Tables\Columns\TextColumn::make('current_balance')
                    ->searchable()
                    ->default('no current balance'),
                Tables\Columns\TextColumn::make('membership')
                    ->searchable()
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Membership' : 'No Membership')
                    ->badge()
                    ->color(fn(bool $state): string => $state ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('reputation')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('delete')
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
        ];
    }

}
