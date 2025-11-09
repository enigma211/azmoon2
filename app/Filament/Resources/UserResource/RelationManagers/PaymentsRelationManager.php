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

    protected static ?string $title = 'تاریخچه پرداخت‌ها';

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
                    
                Tables\Columns\TextColumn::make('amount')
                    ->label('مبلغ')
                    ->money('IRR')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'success' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'success' => 'موفق',
                        'pending' => 'در انتظار',
                        'failed' => 'ناموفق',
                        default => $state,
                    }),
                    
                Tables\Columns\TextColumn::make('authority')
                    ->label('کد پیگیری')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('ref_id')
                    ->label('شماره مرجع')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('paid_at')
                    ->label('تاریخ پرداخت')
                    ->jalaliDateTime()
                    ->sortable(),
                    
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
                        'success' => 'موفق',
                        'pending' => 'در انتظار',
                        'failed' => 'ناموفق',
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
