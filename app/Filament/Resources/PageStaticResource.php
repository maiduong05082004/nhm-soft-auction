<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageStaticResource\Pages;
use App\Models\PageStatic;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;
use App\Enums\CommonConstant;

class PageStaticResource extends Resource
{
    protected static ?string $model = PageStatic::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Trang tĩnh';

    protected static ?string $modelLabel = 'trang tĩnh';

    protected static ?string $pluralModelLabel = 'Trang tĩnh';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(3)
                    ->schema([
                        // Main Content Section
                        Section::make('Thông tin cơ bản')
                            ->schema([
                                TextInput::make('title')
                                    ->required()
                                    ->label('Tiêu đề')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if (! $state) {
                                            return;
                                        }
                                        $baseSlug = \Illuminate\Support\Str::slug($state);
                                        $slug = $baseSlug;
                                        $counter = 1;
                                        while (\App\Models\PageStatic::where('slug', $slug)->exists()) {
                                            $slug = $baseSlug . '-' . $counter++;
                                        }
                                        $set('slug', $slug);
                                    })

                                    ->maxLength(255),

                                TextInput::make('slug')
                                    ->required()
                                    ->readOnly()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),

                                Select::make('status')
                                    ->label('Trạng thái')
                                    ->options([
                                        1 => 'Đăng',
                                        0 => 'Nháp',
                                    ])
                                    ->required()
                                    ->native(false),

                                DateTimePicker::make('published_at')
                                    ->label('Thời gian đăng')
                                    ->helperText('Để trống để đăng sau khi tạo và trạng thái = "Đăng"'),
                            ])
                            ->columnSpan(2),

                        // Sidebar Section
                        Section::make('Media & SEO')
                            ->schema([
                                FileUpload::make('image')
                                    ->label('Ảnh đại diện trang')
                                    ->image()
                                    ->directory('pages')
                                    ->maxSize(2048)
                                    ->imagePreviewHeight('150')
                                    ->helperText('Ảnh sẽ dùng làm banner/og:image nếu không có ảnh khác.')
                                    ->columnSpanFull(),

                                // TextInput::make('template')
                                //     ->label('Template')
                                //     ->helperText('Tên template nếu muốn render layout khác (ví dụ: landing)'),

                                Textarea::make('meta_keywords')
                                    ->label('Meta Keywords')
                                    ->maxLength(255)
                                    ->rows(2),

                                Textarea::make('meta_description')
                                    ->label('Meta Description')
                                    ->rows(3)
                                    ->maxLength(500),
                            ])
                            ->columnSpan(1),
                    ]),

                // Content Section
                Section::make('Nội dung')
                    ->schema([
                        Textarea::make('excerpt')
                            ->label('Miêu tả ngắn')
                            ->rows(3)
                            ->columnSpanFull(),

                        TiptapEditor::make('content')
                            ->label('Nội dung bài viết')
                            ->profile('default')
                            ->required()
                            ->columnSpanFull()
                            ->disk('public')
                            ->directory('uploads/editor')
                            ->acceptedFileTypes([
                                'image/jpeg',
                                'image/png',
                                'image/webp',
                                'image/gif',
                                'application/pdf'
                            ])
                            ->imageResizeMode('force')
                            ->imageResizeTargetWidth('800')
                            ->imageResizeTargetHeight('600')
                            ->extraInputAttributes([
                                'style' => 'min-height: 400px;'
                            ])
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Ảnh')
                    ->circular()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Tiêu đề')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Đường dẫn (URL)')
                    ->toggleable()
                    ->copyable()
                    ->copyMessage('Đã sao chép URL!')
                    ->copyMessageDuration(1500),

                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->formatStateUsing(fn($state): string => $state == 0 ? 'Bản nháp' : 'Đã xuất bản')
                    ->colors([
                        'secondary' => 0,
                        'success'   => 1,
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('Ngày xuất bản')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Ngày cập nhật')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'draft'     => 'Bản nháp',
                        'published' => 'Đã xuất bản',
                    ])
                    ->multiple(),

                Tables\Filters\TrashedFilter::make()
                    ->label('Đã xóa'),

                Tables\Filters\Filter::make('published_range')
                    ->label('Khoảng ngày xuất bản')
                    ->form([
                        DateTimePicker::make('published_from')
                            ->label('Từ ngày')
                            ->native(false),
                        DateTimePicker::make('published_until')
                            ->label('Đến ngày')
                            ->native(false),
                    ])
                    ->query(function ($query, $data) {
                        return $query
                            ->when($data['published_from'], fn($query, $date) => $query->where('published_at', '>=', $date))
                            ->when($data['published_until'], fn($query, $date) => $query->where('published_at', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['published_from'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make('Từ ngày ' . \Carbon\Carbon::parse($data['published_from'])->toFormattedDateString())
                                ->removeField('published_from');
                        }
                        if ($data['published_until'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make('Đến ngày ' . \Carbon\Carbon::parse($data['published_until'])->toFormattedDateString())
                                ->removeField('published_until');
                        }
                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Chỉnh sửa'),
                Tables\Actions\Action::make('visit')
                    ->label('Xem trang')
                    ->url(fn(PageStatic $record): string => route('pages.show', $record->slug))
                    ->openUrlInNewTab()
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->visible(fn(PageStatic $record): bool => $record->status == CommonConstant::INACTIVE),
                Tables\Actions\DeleteAction::make()
                    ->label('Xóa'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Xóa đã chọn'),
                    Tables\Actions\BulkAction::make('publish')
                        ->label('Xuất bản đã chọn')
                        ->icon('heroicon-m-eye')
                        ->requiresConfirmation()
                        ->action(fn($records) => $records->each->update(['status' => CommonConstant::ACTIVE])),
                    Tables\Actions\BulkAction::make('draft')
                        ->label('Chuyển về nháp')
                        ->icon('heroicon-m-eye-slash')
                        ->requiresConfirmation()
                        ->action(fn($records) => $records->each->update(['status' => CommonConstant::INACTIVE])),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListPageStatics::route('/'),
            'create' => Pages\CreatePageStatic::route('/create'),
            'edit' => Pages\EditPageStatic::route('/{record}/edit'),
        ];
    }
}
