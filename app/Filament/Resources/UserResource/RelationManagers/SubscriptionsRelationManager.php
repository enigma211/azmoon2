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

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return 'تاریخچه اشتراک‌ها';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('subscription_plan_id')
                    ->label('پلن اشتراک')
                    ->relationship('subscriptionPlan', 'title')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $plan = \App\Models\SubscriptionPlan::find($state);
                            if ($plan && $plan->duration_days > 0) {
                                $set('ends_at', now()->addDays($plan->duration_days)->format('Y-m-d H:i:s'));
                                $set('days', $plan->duration_days);
                            } elseif ($plan && $plan->duration_days == 0) {
                                // Unlimited
                                $set('ends_at', null);
                                $set('days', null);
                            }
                        }
                    }),

                Forms\Components\TextInput::make('days')
                    ->label('تعداد روز اعتبار')
                    ->numeric()
                    ->helperText('با تغییر این عدد، تاریخ پایان به صورت خودکار محاسبه می‌شود')
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $startDate = $get('starts_at') ? \Carbon\Carbon::parse($get('starts_at')) : now();
                        if ($state > 0) {
                            $set('ends_at', $startDate->copy()->addDays($state)->format('Y-m-d H:i:s'));
                        } else {
                            $set('ends_at', null);
                        }
                    }),

                Forms\Components\DateTimePicker::make('starts_at')
                    ->label('تاریخ شروع')
                    ->default(now())
                    ->required(),

                Forms\Components\DateTimePicker::make('ends_at')
                    ->label('تاریخ پایان')
                    ->helperText('خالی رها کردن به معنی اشتراک نامحدود است'),

                Forms\Components\Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'active' => 'فعال',
                        'expired' => 'منقضی شده',
                        'cancelled' => 'لغو شده',
                    ])
                    ->default('active')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subscriptionPlan.title')
                    ->label('پلن اشتراک')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('starts_at')
                    ->label('تاریخ شروع')
                    ->jalaliDateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('ends_at')
                    ->label('تاریخ پایان')
                    ->jalaliDateTime()
                    ->sortable(),

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
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ ایجاد')
                    ->jalaliDateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('اعطای اشتراک جدید')
                    ->modalHeading('اعطای اشتراک دستی به کاربر'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
