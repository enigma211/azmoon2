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
                        Forms\Components\TextInput::make('username')
                            ->label('نام کاربری (انگلیسی)')
                            ->required()
                            ->rule('regex:/^[A-Za-z0-9]+$/')
                            ->unique(ignoreRecord: true)
                            ->helperText('فقط حروف انگلیسی و اعداد')
                            ->maxLength(50),
                        // Virtual first/last name that hydrate/dehydrate to single name field
                        Forms\Components\TextInput::make('first_name')
                            ->label('نام')
                            ->required()
                            ->afterStateHydrated(function ($set, $record) {
                                $full = (string)($record->name ?? '');
                                $parts = preg_split('/\s+/', trim($full), 2);
                                $set('first_name', $parts[0] ?? '');
                                $set('last_name', $parts[1] ?? '');
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
                            ->maxLength(255),

                        Forms\Components\TextInput::make('mobile')
                            ->label('موبایل')
                            ->tel()
                            ->maxLength(11)
                            ->placeholder('09123456789'),

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

                Forms\Components\Section::make('اشتراک فعال')
                    ->schema([
                        Forms\Components\Placeholder::make('subscription_info')
                            ->label('')
                            ->content(function ($record) {
                                if (!$record || !$record->activeSubscription) {
                                    return 'این کاربر هنوز اشتراکی ندارد. برای افزودن اشتراک، از بخش "اشتراک‌های کاربران" استفاده کنید.';
                                }
                                $sub = $record->activeSubscription;
                                $plan = $sub->subscriptionPlan?->title ?? 'نامشخص';
                                $starts = $sub->starts_at ? jdate($sub->starts_at)->format('Y/m/d') : '-';
                                $ends = $sub->ends_at ? jdate($sub->ends_at)->format('Y/m/d') : 'نامحدود';
                                return "طرح: {$plan} | شروع: {$starts} | پایان: {$ends} | وضعیت: {$sub->status}";
                            })
                            ->visible(fn ($record) => $record !== null),
                    ])
                    ->collapsed()
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['activeSubscription.subscriptionPlan']))
            ->columns([
                Tables\Columns\TextColumn::make('username')
                    ->label('نام کاربری')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('نام')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('ایمیل')
                    ->searchable()
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('mobile')
                    ->label('موبایل')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('role')
                    ->label('نقش')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'admin' => 'مدیر',
                        'student' => 'دانش‌آموز',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn ($state) => $state === 'admin' ? 'danger' : 'success'),

                Tables\Columns\TextColumn::make('activeSubscription.subscriptionPlan.title')
                    ->label('نوع اشتراک')
                    ->default('بدون اشتراک')
                    ->badge()
                    ->color(fn ($state) => $state === 'بدون اشتراک' ? 'gray' : 'success')
                    ->getStateUsing(function ($record) {
                        try {
                            return $record->activeSubscription?->subscriptionPlan?->title ?? 'بدون اشتراک';
                        } catch (\Throwable $e) {
                            return 'بدون اشتراک';
                        }
                    }),

                Tables\Columns\TextColumn::make('activeSubscription.starts_at')
                    ->label('تاریخ خرید')
                    ->jalaliDate()
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        try {
                            return $record->activeSubscription?->starts_at;
                        } catch (\Throwable $e) {
                            return null;
                        }
                    }),

                Tables\Columns\TextColumn::make('activeSubscription.ends_at')
                    ->label('تاریخ پایان')
                    ->jalaliDate()
                    ->default('نامحدود')
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        try {
                            return $record->activeSubscription?->ends_at;
                        } catch (\Throwable $e) {
                            return null;
                        }
                    }),

                Tables\Columns\TextColumn::make('subscription_days_remaining')
                    ->label('روزهای باقیمانده')
                    ->getStateUsing(function ($record) {
                        $subscription = $record->activeSubscription;
                        if (!$subscription) return '-';
                        if (!$subscription->ends_at) return 'نامحدود';
                        try {
                            $endDate = \Carbon\Carbon::parse($subscription->ends_at)->startOfDay();
                            $today = \Carbon\Carbon::now()->startOfDay();
                            $remaining = (int) $today->diffInDays($endDate, false);
                            return $remaining > 0 ? $remaining . ' روز' : 'منقضی شده';
                        } catch (\Throwable $e) {
                            return '-';
                        }
                    })
                    ->badge()
                    ->color(function ($record) {
                        $subscription = $record->activeSubscription;
                        if (!$subscription || !$subscription->ends_at) return 'success';
                        try {
                            $endDate = \Carbon\Carbon::parse($subscription->ends_at)->startOfDay();
                            $today = \Carbon\Carbon::now()->startOfDay();
                            $remaining = (int) $today->diffInDays($endDate, false);
                            if ($remaining <= 0) return 'danger';
                            if ($remaining <= 7) return 'warning';
                            return 'success';
                        } catch (\Throwable $e) {
                            return 'gray';
                        }
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ ثبت‌نام')
                    ->jalaliDateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
