<?php

namespace App\Filament\Resources;

use App\Enums\Permission\RoleConstant;
use App\Filament\Resources\ArticleResource\Pages;
use App\Models\Article;
use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Forms;
use App\Utils\HelperFunc;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;
    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    public static ?string $navigationGroup = 'Tin tức';
    protected static ?string $modelLabel = 'Bài viết';
    protected static ?string $navigationLabel = 'Bài viết';
    protected static ?string $pluralModelLabel = 'Tin tức';
    public static function canAccess(): bool
    {
        return auth()->user()->hasRole(RoleConstant::ADMIN);
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin cơ bản')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Tiêu đề')
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
                            ->label('Đường dẫn')
                            ->required()
                            ->readOnly()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\FileUpload::make('image')
                            ->label('Hình ảnh đại diện')
                            ->image()
                            ->directory('articles')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/jfif'])
                            ->maxSize(2048),
                        SelectTree::make('category_article_id')
                            ->label('Danh mục')
                            ->relationship('category', 'name', 'parent_id')
                            ->searchable()
                            ->formatStateUsing(fn($state) => (string) $state)
                            ->expandSelected(true)
                            ->enableBranchNode()
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Nội dung')
                    ->schema([
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
                    ]),

                Forms\Components\Section::make('Cài đặt')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Trạng thái')
                            ->options([
                                'draft' => 'Nháp',
                                'published' => 'Đã đăng',
                            ])
                            ->default('draft')
                            ->required(),

                        Forms\Components\TextInput::make('sort')
                            ->label('Thứ tự')
                            ->numeric()
                            ->default(0),

                        Forms\Components\TextInput::make('view')
                            ->label('Lượt xem')
                            ->numeric()
                            ->default(0)
                            ->disabled(),
                    ])
                    ->columns(3),
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('seo.title')
                            ->label('SEO Title')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('seo.description')
                            ->label('SEO Description')
                            ->rows(3),
                        Forms\Components\TextInput::make('seo.keywords')
                            ->label('SEO Keywords')
                            ->placeholder('Từ khóa, cách nhau bởi dấu phẩy')
                            ->maxLength(255),
                    ])
                    ->columns(1)
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table->modifyQueryUsing(fn(Builder $query) => $query->with('author', 'category'))
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Hình ảnh')
                    ->getStateUsing(fn($record) => HelperFunc::generateURLFilePath($record->image))
                    ->circular()
                    ->size(60),

                Tables\Columns\TextColumn::make('title')
                    ->label('Tiêu đề')
                    ->color('primary')
                    ->limit(40)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Đường dẫn')
                    ->limit(30)
                    ->searchable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Danh mục')
                    ->formatStateUsing(fn($state) => $state),

                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge(true)
                    ->colors([
                        'success' => 'published',
                        'gray' => 'draft',
                    ])
                    ->formatStateUsing(fn($state): string => match ($state) {
                        'published' => 'Đã đăng',
                        'draft' => 'Nháp',
                        default => $state
                    }),

                Tables\Columns\TextColumn::make('view')
                    ->label('Lượt xem')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('sort')
                    ->label('Thứ tự')
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('author.name')
                    ->label('Tác giả')
                    ->color('danger')
                    ->url(fn($record): string => '/admin/users/' . $record->author->id),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'draft' => 'Nháp',
                        'published' => 'Đã đăng',
                    ]),
                Tables\Filters\Filter::make('view')
                    ->form([
                        Forms\Components\TextInput::make('min_view')
                            ->label('Số lượt xem từ')
                            ->numeric(),
                        Forms\Components\TextInput::make('max_view')
                            ->label('Số lượt xem tới')
                            ->numeric()
                    ])->query(function (Builder $query, array $data) {
                        return $query->when($data['min_view'], fn($q, $value) => $q->where('view', '>=', $value))
                            ->when($data['max_view'], fn($q, $value) => $q->where('view', '<=', $value));
                    }),
                Tables\Filters\Filter::make('publish_time')
                    ->form([
                        Forms\Components\DatePicker::make('start_from')
                            ->label('Đăng từ'),
                        Forms\Components\DatePicker::make('start_to')
                            ->label('Đến'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['start_from'], fn($q, $date) => $q->whereDate('start_time', '>=', $date))
                            ->when($data['start_to'], fn($q, $date) => $q->whereDate('start_time', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Xem'),
                Tables\Actions\EditAction::make()
                    ->label('Sửa'),
                Tables\Actions\DeleteAction::make()
                    ->label('Xóa'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Xóa đã chọn'),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}
