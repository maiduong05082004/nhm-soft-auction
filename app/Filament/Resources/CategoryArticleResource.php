<?php

namespace App\Filament\Resources;

use App\Enums\Permission\RoleConstant;
use App\Filament\Resources\CategoryArticleResource\Pages;
use App\Utils\HelperFunc;
use App\Models\CategoryArticle;
use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategoryArticleResource extends Resource
{
    protected static ?string $model = CategoryArticle::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    public static ?string $navigationGroup = 'Tin tức';
    public static ?string $navigationLabel = 'Danh mục bài viết';
    protected static ?string $modelLabel = 'Danh mục bài viết';
    public static ?int $navigationSort = 2;
    public static function canAccess(): bool
    {
        return auth()->user()->hasRole(RoleConstant::ADMIN);
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Tên danh mục')
                    ->required()
                    ->maxLength(255)
                    ->live(debounce: 1000)
                    ->afterStateUpdated(function ($state, callable $set) {
                        if (!$state) return;
                        $baseSlug = \Illuminate\Support\Str::slug($state);
                        $slug = $baseSlug . '-' . HelperFunc::getTimestampAsId();
                        $set('slug', $slug);
                    }),

                Forms\Components\TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Forms\Components\FileUpload::make('image')
                    ->label('Hình ảnh')
                    ->image()
                    ->directory('categories'),
                SelectTree::make('parent_id')
                    ->label('Danh mục cha')
                    ->withCount()
                    ->searchable()
                    ->formatStateUsing(fn($state) => (string) $state)
                    ->placeholder('Chọn danh mục cha')
                    ->relationship('parent', 'name', 'parent_id')
                    ->expandSelected(true)
                    ->enableBranchNode()
                    ->nullable(),

                TiptapEditor::make('description')
                    ->label('Miêu tả danh mục')
                    ->extraInputAttributes([
                        'style' => 'min-height: 400px;'
                    ])
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\Select::make('status')
                    ->label('Trạng thái')
                    ->options([
                        0 => 'Không hoạt động',
                        1 => 'Hoạt động',
                    ])
                    ->default(1)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Hình ảnh')
                    ->getStateUsing(fn ($record) => HelperFunc::generateURLFilePath($record->image))
                    ->disk('public'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Tên danh mục')
                    ->formatStateUsing(fn($state, $record) => str_repeat('&nbsp;&nbsp;&nbsp;', $record->level) . $state)
                    ->html()
                    ->sortable()
                    ->limit(50)
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('full_path')
                    ->label('Đường dẫn')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('parent.full_path')
                    ->label('Danh mục cha')
                    ->sortable()
                    ->placeholder('Không có danh mục cha')
                    ->limit(50),

                Tables\Columns\TextColumn::make('children_count')
                    ->label('Số lượng danh mục con')
                    ->counts('children')
                    ->sortable(),

                Tables\Columns\TextColumn::make('article_count')
                    ->label('Số lượng bài viết')
                    ->counts('article')
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Mô tả')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->color(fn($state) => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn($state) => $state ? 'Hoạt động' : 'Không hoạt động'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Ngày cập nhật')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        1 => 'Hoạt động',
                        0 => 'Không hoạt động',
                    ]),

                Tables\Filters\SelectFilter::make('parent_id')
                    ->label('Danh mục cha')
                    ->options(function () {
                        $categories = CategoryArticle::all();
                        $options = [];

                        foreach ($categories as $category) {
                            $options[$category->id] = $category->full_path;
                        }

                        return $options;
                    })
                    ->placeholder('Tất cả danh mục'),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Chỉnh sửa'),
                Tables\Actions\ViewAction::make('View')
                    ->label("Xem"),
                Tables\Actions\Action::make('softDelete')
                    ->label('Xóa')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Xác nhận xóa danh mục')
                    ->modalDescription('Bạn có chắc chắn muốn xóa danh mục này? Danh mục sẽ được chuyển vào thùng rác.')
                    ->action(function (CategoryArticle $record) {
                        if ($record->children()->exists()) {
                            Notification::make()
                                ->title('Lỗi')
                                ->body('Không thể xóa danh mục vì nó có danh mục con.')
                                ->danger()
                                ->send();
                            return;
                        }

                        if ($record->article()->exists()) {
                            Notification::make()
                                ->title('Lỗi')
                                ->body('Không thể xóa danh mục vì nó có sản phẩm liên quan.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $record->delete();
                        Notification::make()
                            ->title('Thành công')
                            ->body('Danh mục đã được chuyển vào thùng rác thành công!')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('restore')
                    ->label('Khôi phục')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->visible(fn(CategoryArticle $record): bool => $record->trashed())
                    ->action(function (CategoryArticle $record) {
                        $record->restore();
                        Notification::make()
                            ->title('Thành công')
                            ->body('Danh mục đã được khôi phục thành công!')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Xóa vĩnh viễn')
                        ->requiresConfirmation()
                        ->modalHeading('Xác nhận xóa vĩnh viễn')
                        ->modalDescription('Bạn có chắc chắn muốn xóa vĩnh viễn các danh mục đã chọn? Thao tác này không thể được hoàn tác.')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                if ($record->children()->exists()) {
                                    Notification::make()
                                        ->title('Lỗi')
                                        ->body("Không thể xóa danh mục '{$record->name}' vì nó có danh mục con.")
                                        ->danger()
                                        ->send();
                                    return;
                                }

                                if ($record->article()->exists()) {
                                    Notification::make()
                                        ->title('Lỗi')
                                        ->body("Không thể xóa danh mục '{$record->name}' vì nó có sản phẩm liên quan.")
                                        ->danger()
                                        ->send();
                                    return;
                                }
                            }

                            $records->each->forceDelete();
                            Notification::make()
                                ->title('Thành công')
                                ->body('Các danh mục đã được xóa vĩnh viễn thành công!')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\RestoreBulkAction::make()
                        ->label('Khôi phục')
                        ->action(function ($records) {
                            $records->each->restore();
                            Notification::make()
                                ->title('Thành công')
                                ->body('Các danh mục đã được khôi phục thành công!')
                                ->success()
                                ->send();
                        }),
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
            'index' => Pages\ListCategoryArticles::route('/'),
            'create' => Pages\CreateCategoryArticle::route('/create'),
            'edit' => Pages\EditCategoryArticle::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $ids = collect(\App\Models\CategoryArticle::getTreeList())->pluck('id')->toArray();
        return parent::getEloquentQuery()
            ->whereIn('id', $ids)
            ->orderByRaw('FIELD(id, ' . implode(',', $ids) . ')')
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
