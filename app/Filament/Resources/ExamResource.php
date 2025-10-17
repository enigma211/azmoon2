<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExamResource\Pages;
use App\Filament\Resources\ExamResource\RelationManagers;
use App\Models\Exam;
use App\Filament\Resources\QuestionResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExamResource extends Resource
{
    protected static ?string $model = Exam::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return 'آزمون‌ها';
    }

    public static function getModelLabel(): string
    {
        return 'آزمون';
    }

    public static function getPluralModelLabel(): string
    {
        return 'آزمون‌ها';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'آزمون‌ها';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Fieldset::make('اطلاعات پایه')
                    ->schema([
                        Forms\Components\Select::make('exam_batch_id')
                            ->relationship('batch', 'title')
                            ->label('نوبت آزمون')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\TextInput::make('title')
                            ->label('عنوان آزمون')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('slug')
                            ->label('نامک')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->regex('/^[\p{L}\p{N}\-]+$/u')
                            ->helperText('نامک یکتا برای آزمون. از حروف، اعداد و خط تیره استفاده کنید.'),

                        Forms\Components\Textarea::make('description')
                            ->label('توضیحات')
                            ->rows(4)
                            ->columnSpanFull()
                            ->default('')
                            ->maxLength(1000)
                            ->helperText('توضیحات آزمون (اختیاری)'),
                    ])
                    ->columns(2),

                Forms\Components\Fieldset::make('تنظیمات آزمون')
                    ->schema([
                        Forms\Components\TextInput::make('duration_minutes')
                            ->label('مدت زمان (دقیقه)')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(1000)
                            ->helperText('مدت زمان آزمون به دقیقه'),

                        Forms\Components\TextInput::make('pass_threshold')
                            ->label('نمره قبولی')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->helperText('نمره حداقلی برای قبولی (از 100)'),

                        Forms\Components\Toggle::make('is_published')
                            ->label('منتشر شده')
                            ->default(true)
                            ->helperText('اگر فعال باشد، آزمون برای دانش‌آموزان قابل دسترس است'),
                    ])
                    ->columns(3),

                Forms\Components\Fieldset::make('تنظیمات نمره‌دهی')
                    ->schema([
                        Forms\Components\TextInput::make('total_score')
                            ->label('امتیاز کل')
                            ->numeric()
                            ->default(100)
                            ->required()
                            ->minValue(1)
                            ->step(0.01)
                            ->helperText('امتیاز کل اگر همه سوالات را درست بزند (مثلاً 100)'),

                        Forms\Components\TextInput::make('negative_score_ratio')
                            ->label('نسبت نمره منفی')
                            ->numeric()
                            ->default(3)
                            ->required()
                            ->minValue(0)
                            ->helperText('هر چند سوال غلط، امتیاز یک سوال صحیح را کم می‌کند؟ (مثلاً 3 یعنی هر 3 غلط = -1 صحیح)'),

                        Forms\Components\Placeholder::make('scoring_info')
                            ->label('توضیحات')
                            ->content('سیستم به طور خودکار امتیاز هر سوال را بر اساس تعداد سوالات و امتیاز کل محاسبه می‌کند.')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('عنوان')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('batch.title')
                    ->label('نوبت آزمون')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('duration_minutes')
                    ->label('مدت زمان')
                    ->suffix(' دقیقه')
                    ->sortable()
                    ->numeric(),

                Tables\Columns\TextColumn::make('pass_threshold')
                    ->label('نمره قبولی')
                    ->suffix('/100')
                    ->sortable()
                    ->numeric(decimalPlaces: 2),

                Tables\Columns\TextColumn::make('questions_count')
                    ->label('سوالات')
                    ->getStateUsing(fn ($record) => $record->questions()->where('is_deleted', false)->count())
                    ->url(fn ($record) => QuestionResource::getUrl('index', [
                        'tableFilters' => [
                            'exam_id' => [
                                'value' => $record->id,
                            ],
                        ],
                    ]))
                    ->badge(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('ایجاد')
                    ->formatStateUsing(fn ($state) => jdate_time($state))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // is_published filter removed
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
            'index' => Pages\ListExams::route('/'),
            'create' => Pages\CreateExam::route('/create'),
            'edit' => Pages\EditExam::route('/{record}/edit'),
        ];
    }
}
