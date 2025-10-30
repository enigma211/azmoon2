<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuestionReportResource\Pages;
use App\Filament\Resources\QuestionReportResource\RelationManagers;
use App\Models\QuestionReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuestionReportResource extends Resource
{
    protected static ?string $model = QuestionReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';
    
    protected static ?string $navigationLabel = 'گزارش ایرادات';
    
    protected static ?string $modelLabel = 'گزارش ایراد';
    
    protected static ?string $pluralModelLabel = 'گزارش ایرادات';
    
    protected static ?string $navigationGroup = 'مدیریت محتوا';
    
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('کاربر')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->disabled(),
                Forms\Components\Select::make('question_id')
                    ->label('سوال')
                    ->relationship('question', 'id')
                    ->required()
                    ->disabled(),
                Forms\Components\Select::make('exam_id')
                    ->label('آزمون')
                    ->relationship('exam', 'title')
                    ->searchable()
                    ->disabled(),
                Forms\Components\Textarea::make('report')
                    ->label('متن گزارش')
                    ->required()
                    ->disabled()
                    ->rows(4),
                Forms\Components\Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'pending' => 'در انتظار بررسی',
                        'reviewed' => 'بررسی شده',
                        'resolved' => 'حل شده',
                    ])
                    ->required()
                    ->default('pending'),
                Forms\Components\Textarea::make('admin_notes')
                    ->label('یادداشت مدیر')
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('شناسه')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('کاربر')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('exam.title')
                    ->label('آزمون')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('question_id')
                    ->label('شماره سوال')
                    ->sortable(),
                Tables\Columns\TextColumn::make('report')
                    ->label('گزارش')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('وضعیت')
                    ->colors([
                        'warning' => 'pending',
                        'primary' => 'reviewed',
                        'success' => 'resolved',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'در انتظار',
                        'reviewed' => 'بررسی شده',
                        'resolved' => 'حل شده',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ ثبت')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options([
                        'pending' => 'در انتظار بررسی',
                        'reviewed' => 'بررسی شده',
                        'resolved' => 'حل شده',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListQuestionReports::route('/'),
            'edit' => Pages\EditQuestionReport::route('/{record}/edit'),
        ];
    }
}
