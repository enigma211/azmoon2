<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChoiceResource\Pages;
use App\Filament\Resources\ChoiceResource\RelationManagers;
use App\Models\Choice;
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
use Filament\Tables\Columns\TextColumn;

class ChoiceResource extends Resource
{
    protected static ?string $model = Choice::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return 'گزینه‌ها';
    }

    public static function getModelLabel(): string
    {
        return 'گزینه';
    }

    public static function getPluralModelLabel(): string
    {
        return 'گزینه‌ها';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'بانک سوالات';
    }

    public static function shouldRegisterNavigation(): bool
    {
        // Hide from admin navigation per request; choices are managed inside Question form
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('question_id')
                    ->relationship('question', 'text')
                    ->label('سوال')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('text')
                    ->label('متن گزینه')
                    ->required(),

                Toggle::make('is_correct')
                    ->label('گزینه صحیح')
                    ->default(false),

                TextInput::make('order')
                    ->label('ترتیب')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('question.text')->label('سوال')->limit(40)->searchable(),
                TextColumn::make('text')->label('گزینه')->limit(40)->searchable(),
                TextColumn::make('is_correct')->label('صحیح')->formatStateUsing(fn ($state) => $state ? 'بله' : 'خیر'),
                TextColumn::make('order')->label('ترتیب')->sortable(),
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
            'index' => Pages\ListChoices::route('/'),
            'create' => Pages\CreateChoice::route('/create'),
            'edit' => Pages\EditChoice::route('/{record}/edit'),
        ];
    }
}
