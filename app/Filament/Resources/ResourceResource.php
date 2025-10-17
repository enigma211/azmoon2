<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResourceResource\Pages;
use App\Filament\Resources\ResourceResource\RelationManagers;
use App\Models\ResourceItem as ResourceModel;
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
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;

class ResourceResource extends Resource
{
    protected static ?string $model = ResourceModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    public static function getNavigationLabel(): string
    {
        return 'منابع آموزشی';
    }

    public static function getModelLabel(): string
    {
        return 'منبع آموزشی';
    }

    public static function getPluralModelLabel(): string
    {
        return 'منابع آموزشی';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'محتوا';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('اطلاعات منبع')
                    ->schema([
                        Select::make('type')
                            ->label('نوع منبع')
                            ->options([
                                'file' => 'فایل',
                                'link' => 'لینک',
                                'image' => 'تصویر',
                                'pdf' => 'PDF',
                                'video' => 'ویدیو',
                            ])
                            ->default('file')
                            ->required(),

                        TextInput::make('title')
                            ->label('عنوان')
                            ->required()
                            ->maxLength(255),

                        Textarea::make('description')
                            ->label('توضیحات')
                            ->rows(3)
                            ->nullable(),

                        FileUpload::make('file_path')
                            ->label('فایل / لینک')
                            ->helperText('برای نوع لینک، می‌توانید URL را به‌صورت متن وارد کنید.')
                            ->directory('resources')
                            ->preserveFilenames()
                            ->openable()
                            ->downloadable()
                            ->nullable(),
                    ])->columns(2),

                Section::make('اتصالات آزمون (اختیاری)')
                    ->schema([
                        Select::make('exam_domain_id')
                            ->relationship('domain', 'title')
                            ->label('دامنه')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Select::make('exam_batch_id')
                            ->relationship('batch', 'title')
                            ->label('نوبت')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Select::make('exam_id')
                            ->relationship('exam', 'title')
                            ->label('آزمون')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('عنوان')->searchable()->sortable(),
                TextColumn::make('type')->label('نوع')->sortable(),
                TextColumn::make('domain.title')->label('دامنه')->toggleable(),
                TextColumn::make('batch.title')->label('نوبت')->toggleable(),
                TextColumn::make('exam.title')->label('آزمون')->toggleable(),
                TextColumn::make('updated_at')
                    ->label('ویرایش')
                    ->formatStateUsing(fn ($state) => jdate_time($state))
                    ->sortable(),
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
            'index' => Pages\ListResources::route('/'),
            'create' => Pages\CreateResource::route('/create'),
            'edit' => Pages\EditResource::route('/{record}/edit'),
        ];
    }
}
