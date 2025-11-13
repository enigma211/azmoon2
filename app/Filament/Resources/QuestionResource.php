<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuestionResource\Pages;
use App\Filament\Resources\QuestionResource\RelationManagers;
use App\Models\Question;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    public static function getNavigationLabel(): string
    {
        return 'سوالات';
    }

    public static function getModelLabel(): string
    {
        return 'سوال';
    }

    public static function getPluralModelLabel(): string
    {
        return 'سوالات';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'آزمون‌ها';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('exam_id')
                    ->relationship('exam', 'title')
                    ->label('آزمون')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('order_column')
                    ->label('شماره سوال')
                    ->numeric()
                    ->default(0)
                    ->required(),

                Forms\Components\Hidden::make('type')
                    ->default('single_choice'),

                Forms\Components\Hidden::make('difficulty')
                    ->default('easy'),

                Forms\Components\Hidden::make('score')
                    ->default(1),

                Forms\Components\Hidden::make('negative_score')
                    ->default(0),

                Forms\Components\Grid::make(12)
                    ->schema([
                        RichEditor::make('text')
                            ->label('متن سوال (پشتیبانی فرمول)')
                            ->required()
                            ->toolbarButtons([
                                'bold','italic','strike','underline','link','orderedList','bulletList','codeBlock'
                            ])
                            ->columnSpan(8),

                        Forms\Components\Group::make()
                            ->schema([
                                FileUpload::make('image_path')
                                    ->label('تصویر سوال (اختیاری)')
                                    ->image()
                                    ->directory('questions')
                                    ->acceptedFileTypes(['image/jpeg','image/png'])
                                    ->openable()
                                    ->downloadable()
                                    ->imageEditor()
                                    ->nullable(),

                                Toggle::make('is_deleted')
                                    ->label('سوال حذف شده')
                                    ->helperText('در صورت فعال بودن، این سوال به کاربر نمایش داده می‌شود اما در محاسبه نمره لحاظ نمی‌گردد')
                                    ->inline(false)
                                    ->default(false),
                            ])
                            ->columnSpan(4),
                    ]),

                Repeater::make('choices')
                    ->label('گزینه‌ها')
                    ->relationship()
                    ->minItems(4)
                    ->maxItems(4)
                    ->default([
                        ['text' => '', 'is_correct' => false, 'order' => 1],
                        ['text' => '', 'is_correct' => false, 'order' => 2],
                        ['text' => '', 'is_correct' => false, 'order' => 3],
                        ['text' => '', 'is_correct' => false, 'order' => 4],
                    ])
                    ->schema([
                        TextInput::make('text')
                            ->label(fn (callable $get) => 'گزینه ' . ($get('order') ?? 1))
                            ->required(),
                        Toggle::make('is_correct')
                            ->label('گزینه صحیح')
                            ->inline(false)
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if ($state !== true) return;
                                $order = (int)($get('order') ?? 0);
                                for ($i = 1; $i <= 4; $i++) {
                                    if ($i !== $order) {
                                        $set('choices.' . ($i - 1) . '.is_correct', false);
                                    }
                                }
                            })
                            ->disabled(function ($state, callable $get) {
                                // If another item is already marked correct, disable this toggle unless it's the one already true
                                $choices = collect($get('../../choices') ?? []);
                                $hasTrue = $choices->filter(fn($c) => ($c['is_correct'] ?? false) === true)->count() >= 1;
                                return $hasTrue && !$state; // disable others when one is already true
                            })
                            ->default(false),
                        TextInput::make('order')->numeric()->label('ترتیب')->default(0)->hidden(),
                    ])
                    ->grid(1)
                    ->orderColumn('order')
                    ->reorderable(false)
                    ->collapsible(false)
                    ->dehydrated(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_column')->label('شماره')->sortable(),
                TextColumn::make('text')
                    ->label('متن سوال')
                    ->html()
                    ->searchable()
                    ->wrap()
                    ->words(20),
                TextColumn::make('correct_answer')
                    ->label('پاسخ صحیح')
                    ->getStateUsing(function (Question $record) {
                        $correctChoice = $record->choices()->where('is_correct', true)->first();
                        return $correctChoice ? 'گزینه ' . $correctChoice->order : '-';
                    }),
                TextColumn::make('is_deleted')
                    ->label('وضعیت')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'danger' : 'success')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'حذف شده' : 'فعال'),
                TextColumn::make('exam.title')->label('آزمون')->searchable(),
                TextColumn::make('created_at')->label('تاریخ ایجاد')->formatStateUsing(fn ($state) => jdate_time($state))->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('exam_id')
                    ->relationship('exam', 'title')
                    ->label('آزمون')
                    ->searchable()
                    ->preload()
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListQuestions::route('/'),
            'create' => Pages\CreateQuestion::route('/create'),
            'edit' => Pages\EditQuestion::route('/{record}/edit'),
        ];
    }
}

