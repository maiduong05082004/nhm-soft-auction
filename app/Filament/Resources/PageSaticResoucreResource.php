<?php

namespace App\Filament\Resources;

use App\Models\PageStatic;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PageStaticResource extends Resource
{
    protected static ?string $model = PageStatic::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Website';
    protected static ?int $navigationSort = 10;
    protected static ?string $navigationLabel = 'Pages';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Grid::make(3)->schema([
                    Card::make()->schema([
                        TextInput::make('title')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                // Nếu slug rỗng hoặc slug là slug dạng cũ, cập nhật slug tự động
                                if (empty($set) || ! $state) {
                                    return;
                                }
                                $set('slug', Str::slug($state));
                            })
                            ->maxLength(255),

                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(PageStatic::class, 'slug', ignoreRecord: true)
                            ->helperText('URL thân thiện, ví dụ: gioi-thieu'),

                        Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Published',
                            ])
                            ->default('draft')
                            ->required(),

                        DateTimePicker::make('published_at')
                            ->label('Published at')
                            ->helperText('Để trống để publish ngay khi status = published'),
                    ])->columnSpan(2),

                    Card::make()->schema([
                        FileUpload::make('image')
                            ->label('Banner / Featured image')
                            ->image()
                            ->directory('pages')
                            ->maxSize(2048)
                            ->imagePreviewHeight('150')
                            ->helperText('Ảnh sẽ dùng làm banner/og:image nếu không có ảnh khác.'),

                        TextInput::make('template')
                            ->label('Template')
                            ->helperText('Tên template nếu muốn render layout khác (ví dụ: landing)'),

                        TextInput::make('meta_title')
                            ->label('Meta Title')
                            ->maxLength(255),

                        Textarea::make('meta_keywords')
                            ->label('Meta Keywords')
                            ->maxLength(255),

                        Textarea::make('meta_description')
                            ->label('Meta Description')
                            ->rows(3)
                            ->maxLength(500),
                    ])->columnSpan(1),
                ]),

                Card::make()->schema([
                    Textarea::make('excerpt')
                        ->label('Excerpt / Short description')
                        ->rows(3),
                    RichEditor::make('content')
                        ->label('Content')
                        ->toolbarButtons([
                            'bold', 'italic', 'underline', 'strike', 'link', 'blockquote', 'bulletList', 'numberList', 'redo', 'undo', 'code', 'codeBlock', 'h2', 'h3'
                        ])
                        ->required(),
                ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('#')->sortable(),
                Tables\Columns\TextColumn::make('title')->searchable()->sortable()->limit(50),
                Tables\Columns\TextColumn::make('slug')->label('URL')->toggleable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->enum([
                        'draft' => 'Draft',
                        'published' => 'Published',
                    ])
                    ->colors([
                        'secondary' => 'draft',
                        'success' => 'published',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Published at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Created')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                    ]),
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\Filter::make('published_range')
                    ->form([
                        DateTimePicker::make('published_from')->label('From'),
                        DateTimePicker::make('published_until')->label('Until'),
                    ])
                    ->query(function ($query, $data) {
                        if ($data['published_from']) {
                            $query->where('published_at', '>=', $data['published_from']);
                        }
                        if ($data['published_until']) {
                            $query->where('published_at', '<=', $data['published_until']);
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('view')
                    ->label('View')
                    ->url(fn (PageStatic $record): string => route('pages.show', $record->slug))
                    ->openUrlInNewTab(),
                Tables\Actions\DeleteAction::make(),
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

    /**
     * Dùng các Page base của Filament (ListRecords/CreateRecord/EditRecord)
     * Nếu bạn muốn custom pages, bạn có thể generate bằng artisan và chỉnh ở App\Filament\Resources\PageStaticResource\Pages\...
     */
    public static function getPages(): array
    {
        return [
            'index' => \Filament\Resources\Pages\ListRecords::route('/'),
            'create' => \Filament\Resources\Pages\CreateRecord::route('/create'),
            'edit' => \Filament\Resources\Pages\EditRecord::route('/{record}/edit'),
        ];
    }
}
