<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $recordTitleAttribute = 'id';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return 'تاریخچه پرداخت‌ها';
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
                Tables\Columns\TextColumn::make('id')
                    ->label('شناسه')
                    ->sortable(),
                Tables\Columns\TextColumn::make('subscriptionPlan.title')
                    ->label('پلن اشتراک')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('مبلغ (تومان)')
                    ->formatStateUsing(fn ($state) => number_format($state) . ' تومان')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'success' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        'canceled' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'success' => 'موفق',
                        'pending' => 'در انتظار',
                        'failed' => 'ناموفق',
                        'canceled' => 'لغو شده',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('ref_id')
                    ->label('کد پیگیری')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('کد پیگیری کپی شد')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('authority')
                    ->label('شناسه تراکنش')
                    ->searchable()
                    ->limit(20)
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ ایجاد')
                    ->jalaliDate()
                    ->sortable(),
                Tables\Columns\TextColumn::make('paid_at')
                    ->label('تاریخ پرداخت')
                    ->jalaliDate()
                    ->sortable()
                    ->placeholder('-'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options([
                        'success' => 'موفق',
                        'pending' => 'در انتظار',
                        'failed' => 'ناموفق',
                        'canceled' => 'لغو شده',
                    ]),
            ]);
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
