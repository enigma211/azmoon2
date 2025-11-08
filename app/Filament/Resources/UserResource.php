<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function getNavigationLabel(): string
    {
        return 'کاربران';
    }

    public static function getModelLabel(): string
    {
        return 'کاربر';
    }

    public static function getPluralModelLabel(): string
    {
        return 'کاربران';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'کاربران';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('اطلاعات کاربر')
                    ->schema([
                        Forms\Components\TextInput::make('mobile')
                            ->label('موبایل')
                            ->tel()
                            ->required()
                            ->maxLength(11)
                            ->placeholder('09123456789')
                            ->helperText('نام کاربری به صورت خودکار همان شماره موبایل است'),

                        Forms\Components\TextInput::make('username')
                            ->label('نام کاربری (خودکار)')
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('نام کاربری از شماره موبایل گرفته می‌شود'),

                        // Virtual first/last name that hydrate/dehydrate to single name field
                        Forms\Components\TextInput::make('first_name')
                            ->label('نام')
                            ->required()
                            ->afterStateHydrated(function ($set, $record) {
                                if ($record && $record->name) {
                                    $full = trim((string)$record->name);
                                    if (!empty($full)) {
                                        $parts = preg_split('/\s+/', $full, 2);
                                        $set('first_name', $parts[0] ?? '');
                                        $set('last_name', $parts[1] ?? '');
                                    } else {
                                        $set('first_name', '');
                                        $set('last_name', '');
                                    }
                                } else {
                                    $set('first_name', '');
                                    $set('last_name', '');
                                }
                            })
                            ->dehydrated(false)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('last_name')
                            ->label('نام خانوادگی')
                            ->required()
                            ->dehydrated(false)
                            ->maxLength(255),

                        // Hidden real field mapped from first/last name on save
                        Forms\Components\Hidden::make('name')
                            ->dehydrateStateUsing(function (callable $get) {
                                $fn = trim((string)$get('first_name'));
                                $ln = trim((string)$get('last_name'));
                                return trim($fn . ' ' . $ln);
                            })
                            ->required(),

                        Forms\Components\TextInput::make('email')
                            ->label('ایمیل')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\Select::make('role')
                            ->label('نقش')
                            ->options([
                                'student' => 'دانش‌آموز',
                                'admin' => 'مدیر',
                            ])
                            ->default('student')
                            ->required(),

                        Forms\Components\TextInput::make('password')
                            ->label('رمز عبور')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => $state ? bcrypt($state) : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->helperText('برای ویرایش خالی بگذارید تا تغییر نکند'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('مدیریت اشتراک ویژه')
                    ->schema([
                        Forms\Components\Toggle::make('grant_subscription')
                            ->label('اعطای اشتراک ویژه')
                            ->helperText('با فعال کردن این گزینه، می‌توانید به کاربر اشتراک ویژه بدهید')
                            ->live()
                            ->dehydrated(false),

                        Forms\Components\TextInput::make('subscription_days')
                            ->label('تعداد روز اشتراک')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(3650)
                            ->default(90)
                            ->suffix('روز')
                            ->helperText('پیش‌فرض: 90 روز (3 ماه)')
                            ->visible(fn (callable $get) => $get('grant_subscription'))
                            ->dehydrated(false),

                        Forms\Components\Placeholder::make('current_subscription')
                            ->label('اشتراک فعلی')
                            ->content(function ($record) {
                                if (!$record) return 'کاربر جدید - اشتراکی ندارد';
                                
                                $subscription = \App\Models\UserSubscription::where('user_id', $record->id)
                                    ->where('status', 'active')
                                    ->latest('starts_at')
                                    ->first();
                                
                                if (!$subscription) return 'بدون اشتراک فعال';
                                
                                $plan = \App\Models\SubscriptionPlan::find($subscription->subscription_plan_id);
                                $planTitle = $plan?->title ?? 'نامشخص';
                                $starts = $subscription->starts_at ? jdate($subscription->starts_at)->format('Y/m/d') : '-';
                                $ends = $subscription->ends_at ? jdate($subscription->ends_at)->format('Y/m/d') : 'نامحدود';
                                
                                return "📦 {$planTitle} | 📅 از {$starts} تا {$ends}";
                            }),
                    ])
                    ->columns(1)
                    ->visible(fn (string $operation) => $operation === 'edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('شناسه')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('mobile')
                    ->label('نام کاربری (موبایل)')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->description(fn ($record) => $record->username),
                    
                Tables\Columns\TextColumn::make('name')
                    ->label('نام')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('ایمیل')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-m-envelope'),

                Tables\Columns\TextColumn::make('role')
                    ->label('نقش')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'admin' => 'مدیر',
                        'student' => 'دانش‌آموز',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn ($state) => $state === 'admin' ? 'danger' : 'success'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ ثبت‌نام')
                    ->jalaliDateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('نقش')
                    ->options([
                        'admin' => 'مدیر',
                        'student' => 'دانش‌آموز',
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
