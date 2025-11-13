<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionPlanResource\Pages;
use App\Filament\Resources\SubscriptionPlanResource\RelationManagers;
use App\Models\SubscriptionPlan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubscriptionPlanResource extends Resource
{
    protected static ?string $model = SubscriptionPlan::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return 'پلن‌های اشتراک';
    }

    public static function getModelLabel(): string
    {
        return 'پلن اشتراک';
    }

    public static function getPluralModelLabel(): string
    {
        return 'پلن‌های اشتراک';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'کاربران';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('اطلاعات پلن اشتراک')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('عنوان پلن')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('مثال: پلن رایگان، پلن 3 ماهه، پلن 6 ماهه'),

                        Forms\Components\Textarea::make('description')
                            ->label('توضیحات')
                            ->rows(3)
                            ->maxLength(1000)
                            ->placeholder('توضیحات مربوط به این پلن اشتراک')
                            ->nullable(),

                        Forms\Components\TextInput::make('price_toman')
                            ->label('قیمت (تومان)')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->helperText('برای پلن رایگان عدد 0 وارد کنید')
                            ->nullable(),

                        Forms\Components\TextInput::make('duration_days')
                            ->label('مدت اعتبار (روز)')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->helperText('مثال: 90 روز = 3 ماه، 180 روز = 6 ماه، 0 = نامحدود')
                            ->default(0),

                        Forms\Components\Select::make('access_scope')
                            ->label('دامنه دسترسی')
                            ->options([
                                'all' => 'دسترسی کامل',
                                'domain' => 'محدود به دامنه',
                                'batch' => 'محدود به نوبت',
                            ])
                            ->default('all')
                            ->required()
                            ->helperText('پلن‌های پولی معمولاً دسترسی کامل دارند'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('فعال')
                            ->default(true)
                            ->helperText('آیا این پلن برای خرید فعال است؟')
                            ->inline(false),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('عنوان')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('price_toman')
                    ->label('قیمت')
                    ->formatStateUsing(fn ($state) => $state == 0 ? 'رایگان' : number_format($state) . ' تومان')
                    ->sortable(),

                Tables\Columns\TextColumn::make('duration_days')
                    ->label('مدت اعتبار')
                    ->formatStateUsing(fn ($state) => $state == 0 ? 'نامحدود' : $state . ' روز (' . round($state / 30, 1) . ' ماه)')
                    ->sortable(),

                Tables\Columns\TextColumn::make('access_scope')
                    ->label('دسترسی')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'all' => 'کامل',
                        'domain' => 'دامنه',
                        'batch' => 'نوبت',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn ($state) => $state === 'all' ? 'success' : 'warning'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('وضعیت')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('active_users_count')
                    ->label('کاربران فعال این پلن')
                    ->sortable()
                    ->alignCenter()
                    ->tooltip('تعداد کاربران با اشتراک فعال این پلن (status=active و تاریخ انقضا نگذشته)')
                    ->default(0),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ ایجاد')
                    ->formatStateUsing(fn ($state) => jdate_time($state))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $query->withCount([
                    'userSubscriptions as active_users_count' => function ($q) {
                        $q->where('status', 'active')
                          ->where(function ($qq) {
                              $qq->whereNull('ends_at')
                                 ->orWhere('ends_at', '>', now());
                          })
                          ->distinct('user_id');
                    },
                ]);
            })
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('فعال')
                    ->placeholder('همه')
                    ->trueLabel('فعال')
                    ->falseLabel('غیرفعال'),
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
            'index' => Pages\ListSubscriptionPlans::route('/'),
            'create' => Pages\CreateSubscriptionPlan::route('/create'),
            'edit' => Pages\EditSubscriptionPlan::route('/{record}/edit'),
        ];
    }
}
