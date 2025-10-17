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
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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
            'index' => Pages\ListExamBatches::route('/'),
            'create' => Pages\CreateExamBatch::route('/create'),
            'edit' => Pages\EditExamBatch::route('/{record}/edit'),
        ];
    }
}
