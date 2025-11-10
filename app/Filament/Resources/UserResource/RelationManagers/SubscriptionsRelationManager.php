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

    protected static ?string $recordTitleAttribute = 'id';

    public static function getTitle(): string
    {
        return 'تاریخچه اشتراک‌ها';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // This form is read-only as it's for history.
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subscriptionPlan.title')
                    ->label('پلن اشتراک'),
                Tables\Columns\TextColumn::make('starts_at')
                    ->label('تاریخ شروع')
                    ->date('Y/m/d'),
                Tables\Columns\TextColumn::make('ends_at')
                    ->label('تاریخ پایان')
                    ->date('Y/m/d'),
                Tables\Columns\TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'expired' => 'danger',
                        'cancelled' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'فعال',
                        'expired' => 'منقضی شده',
                        'cancelled' => 'لغو شده',
                        default => $state,
                    }),
            ])
            ->defaultSort('starts_at', 'desc');
    }

    public function canCreate(): bool
    {
        return false;
    }

    public function canEdit($record): bool
    {
        return false;
    }

    public function canDelete($record): bool
    { 
        return false;
    }

    public function canDeleteAny(): bool
    {
        return false;
    }
}
