<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Post;
use Filament\Forms;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationLabel = 'لیست اخبار';
    protected static ?string $modelLabel = 'خبر';
    protected static ?string $pluralModelLabel = 'اخبار';
    protected static ?string $navigationGroup = 'اخبار و مقالات';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->label('دسته‌بندی')
                            ->relationship('category', 'title')
                            ->required()
                            ->preload()
                            ->searchable()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('title')->label('عنوان')->required(),
                                Forms\Components\TextInput::make('slug')->label('اسلاگ')->required(),
                            ]),

                        Forms\Components\TextInput::make('title')
                            ->label('عنوان خبر')
                            ->required(),

                        Forms\Components\TextInput::make('slug')
                            ->label('آدرس یکتا (Slug)')
                            ->default(fn () => 'post-' . rand(1000, 9999))
                            ->readOnly()
                            ->required()
                            ->unique(Post::class, 'slug', ignoreRecord: true),

                        Forms\Components\Textarea::make('summary')
                            ->label('خلاصه خبر')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('content')
                            ->label('متن کامل خبر')
                            ->columnSpanFull()
                            ->fileAttachmentsDirectory('posts'),

                        Forms\Components\TagsInput::make('meta_keywords')
                            ->label('کلمات کلیدی سئو (اینتر بزنید)')
                            ->separator(',')
                            ->suggestions(function () {
                                $keywords = Post::pluck('meta_keywords')->filter()->flatMap(function ($item) {
                                    return explode(',', $item);
                                })->unique()->values()->toArray();
                                return $keywords;
                            }),

                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('تاریخ انتشار')
                            ->default(now()),

                        Forms\Components\Toggle::make('is_published')
                            ->label('منتشر شده')
                            ->default(true),
                    ])->columns(2),

                Forms\Components\Section::make('تصویر شاخص')
                    ->schema([
                        Forms\Components\FileUpload::make('image_path')
                            ->label('تصویر')
                            ->image()
                            ->directory('posts')
                            ->columnSpanFull(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('تصویر'),
                Tables\Columns\TextColumn::make('title')
                    ->label('عنوان')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.title')
                    ->label('دسته‌بندی')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('وضعیت')
                    ->boolean(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('تاریخ انتشار')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('view_count')
                    ->label('بازدید')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
