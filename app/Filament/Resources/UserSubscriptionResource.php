<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserSubscriptionResource\Pages;
use App\Filament\Resources\UserSubscriptionResource\RelationManagers;
use App\Models\UserSubscription;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
 

class UserSubscriptionResource extends Resource
{
    protected static ?string $model = UserSubscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return 'اشتراک‌های کاربران';
    }

    public static function getModelLabel(): string
    {
        return 'اشتراک کاربر';
    }

    public static function getPluralModelLabel(): string
    {
        return 'اشتراک‌های کاربران';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'کاربران';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('اطلاعات اشتراک کاربر')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('کاربر')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('subscription_plan_id')
                            ->label('پلن اشتراک')
                            ->relationship('subscriptionPlan', 'title')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, \Filament\Forms\Get $get) {
                                // Recompute ends_at when plan changes
                                if (! $state) return;
                                $durationDays = \App\Models\SubscriptionPlan::where('id', $state)->value('duration_days');
                                if (! $durationDays || (int)$durationDays <= 0) {
                                    $set('ends_at', null);
                                    return;
                                }
                                $startVal = $get('starts_at');
                                if ($startVal) {
                                    try {
                                        $start = \Illuminate\Support\Carbon::parse($startVal);
                                        $end = $start->copy()->addDays((int)$durationDays);
                                        $set('ends_at', $end->format('Y-m-d'));
                                    } catch (\Throwable $e) {
                                        // ignore parse errors
                                    }
                                }
                            }),

                        Forms\Components\DatePicker::make('starts_at')
                            ->label('تاریخ شروع اشتراک')
                            ->jalali()
                            ->displayFormat('Y/m/d')
                            ->format('Y-m-d')
                            ->reactive()
                            ->required()
                            ->afterStateUpdated(function ($state, callable $set, \Filament\Forms\Get $get) {
                                // If a plan is selected and ends_at is empty, compute it
                                $planId = $get('subscription_plan_id');
                                if (! $planId) return;
                                $durationDays = \App\Models\SubscriptionPlan::where('id', $planId)->value('duration_days');
                                if (! $durationDays || (int)$durationDays <= 0) {
                                    // Unlimited or not set
                                    $set('ends_at', null);
                                    return;
                                }
                                if ($state) {
                                    try {
                                        $start = \Illuminate\Support\Carbon::parse($state);
                                        $end = $start->copy()->addDays((int)$durationDays);
                                        $set('ends_at', $end->format('Y-m-d'));
                                    } catch (\Throwable $e) {
                                        // ignore parse errors
                                    }
                                }
                            }),

                        Forms\Components\DatePicker::make('ends_at')
                            ->label('تاریخ پایان اشتراک')
                            ->jalali()
                            ->displayFormat('Y/m/d')
                            ->format('Y-m-d')
                            ->native(false)
                            ->helperText('اگر پلن مدت‌دار باشد، به‌صورت خودکار از تاریخ شروع محاسبه می‌شود')
                            ->reactive()
                            ->nullable(),

                        Forms\Components\Select::make('status')
                            ->label('وضعیت')
                            ->options([
                                'active' => 'فعال',
                                'pending' => 'منتظر پرداخت/تایید',
                                'cancelled' => 'لغوشده',
                                'expired' => 'منقضی',
                            ])
                            ->default('active')
                            ->required()
                            ->native(false),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('کاربر')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('subscriptionPlan.title')
                    ->label('پلن')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('وضعیت')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'pending',
                        'danger' => 'cancelled',
                        'gray' => 'expired',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('starts_at')
                    ->label('شروع')
                    ->jalaliDate()
                    ->sortable(),

                Tables\Columns\TextColumn::make('ends_at')
                    ->label('پایان')
                    ->jalaliDate()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options([
                        'active' => 'فعال',
                        'pending' => 'منتظر پرداخت/تایید',
                        'cancelled' => 'لغوشده',
                        'expired' => 'منقضی',
                    ]),
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
            'index' => Pages\ListUserSubscriptions::route('/'),
            'create' => Pages\CreateUserSubscription::route('/create'),
            'edit' => Pages\EditUserSubscription::route('/{record}/edit'),
        ];
    }
}
