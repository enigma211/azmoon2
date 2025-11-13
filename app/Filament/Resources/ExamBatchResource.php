<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExamBatchResource\Pages;
use App\Filament\Resources\ExamBatchResource\RelationManagers;
use App\Models\ExamBatch;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;

class ExamBatchResource extends Resource
{
    protected static ?string $model = \App\Models\ExamBatch::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return 'نوبت‌های آزمون';
    }

    public static function getModelLabel(): string
    {
        return 'نوبت آزمون';
    }

    public static function getPluralModelLabel(): string
    {
        return 'نوبت‌های آزمون';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'آزمون‌ها';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('اطلاعات نوبت')
                    ->schema([
                        Select::make('exam_domain_id')
                            ->relationship('domain', 'title')
                            ->label('دامنه آزمون')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('title')
                            ->label('عنوان')
                            ->required(),

                        TextInput::make('slug')
                            ->label('نامک')
                            ->helperText('در صورت خالی بودن می‌تواند خودکار تولید شود.')
                            ->nullable(),

                        Toggle::make('is_active')
                            ->label('فعال')
                            ->default(true),
                        
                        TextInput::make('sort_order')
                            ->label('ترتیب نمایش')
                            ->numeric()
                            ->default(0)
                            ->helperText('عدد کوچکتر = نمایش زودتر. برای مثال: 1، 2، 3، ...')
                            ->required(),

                        Toggle::make('auto_generate_engineering_exams')
                            ->label('ایجاد خودکار 16 آزمون نظام مهندسی')
                            ->helperText('با فعال کردن این گزینه، 16 آزمون مرتبط با نظام مهندسی به صورت خودکار ایجاد می‌شود.')
                            ->default(false)
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('تنظیمات سئو')
                    ->description('این اطلاعات برای بهبود رتبه صفحه در موتورهای جستجو استفاده می‌شود.')
                    ->schema([
                        TextInput::make('seo_title')
                            ->label('عنوان سئو (Meta Title)')
                            ->maxLength(60)
                            ->helperText('توصیه می‌شود بین 50 تا 60 کاراکتر باشد.'),
                        Forms\Components\Textarea::make('seo_description')
                            ->label('توضیحات سئو (Meta Description)')
                            ->rows(3)
                            ->maxLength(250)
                            ->helperText('توصیه می‌شود بین 200 تا 250 کاراکتر باشد.'),
                    ])->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')
                    ->label('ترتیب')
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('title')->label('عنوان')->sortable()->searchable(),
                TextColumn::make('domain.title')->label('دامنه')->sortable()->searchable(),
                TextColumn::make('updated_at')->formatStateUsing(fn ($state) => jdate_time($state))->label('ویرایش')->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order', 'asc');
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
            'index' => Pages\ListExamBatches::route('/'),
            'create' => Pages\CreateExamBatch::route('/create'),
            'edit' => Pages\EditExamBatch::route('/{record}/edit'),
        ];
    }
}
