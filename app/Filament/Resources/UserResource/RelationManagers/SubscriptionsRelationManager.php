<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubscriptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'subscriptions';

    protected static ?string $title = 'تاریخچه اشتراک‌ها';

    protected static ?string $recordTitleAttribute = 'id';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('شناسه')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('subscriptionPlan.title')
                    ->label('پلن')
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('subscriptionPlan.price_toman')
                    ->label('قیمت')
                    ->money('IRR')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'expired' => 'danger',
                        'inactive' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'فعال',
                        'expired' => 'منقضی شده',
                        'inactive' => 'غیرفعال',
                        default => $state,
                    }),
                    
                Tables\Columns\TextColumn::make('starts_at')
                    ->label('تاریخ شروع')
                    ->jalaliDate()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('ends_at')
                    ->label('تاریخ پایان')
                    ->jalaliDate()
                    ->sortable()
                    ->getStateUsing(fn ($record) => $record->ends_at ? $record->ends_at : 'نامحدود'),
                    
                Tables\Columns\TextColumn::make('days_remaining')
                    ->label('روزهای باقیمانده')
                    ->getStateUsing(function ($record) {
                        if (!$record->ends_at) return 'نامحدود';
                        
                        $daysLeft = now()->diffInDays($record->ends_at, false);
                        if ($daysLeft > 0) {
                            return ceil($daysLeft) . ' روز';
                        } else {
                            return 'منقضی شده (' . abs(floor($daysLeft)) . ' روز پیش)';
                        }
                    })
                    ->badge()
                    ->color(fn ($record) => !$record->ends_at ? 'success' : (now()->diffInDays($record->ends_at, false) > 0 ? 'success' : 'danger')),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ ایجاد')
                    ->jalaliDateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options([
                        'active' => 'فعال',
                        'expired' => 'منقضی شده',
                        'inactive' => 'غیرفعال',
                    ]),
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
