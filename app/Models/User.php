<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'mobile',
        'role',
        'font_size',
        'theme',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Allow only admin role to access Filament panels.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Get user's subscriptions
     */
    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    /**
     * Get user's active subscription
     */
    public function activeSubscription()
    {
        return $this->hasOne(UserSubscription::class)
            ->where('status', 'active')
            ->where(function($query) {
                $query->whereNull('ends_at')
                      ->orWhere('ends_at', '>', now());
            })
            ->latest('starts_at');
    }

    /**
     * Get user's payments
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get user's push notification subscriptions
     */
    public function pushSubscriptions()
    {
        return $this->hasMany(PushSubscription::class);
    }

    /**
     * Get user's subscription plan
     */
    public function subscriptionPlan()
    {
        // Return the plan of the active subscription
        return $this->hasOneThrough(
            SubscriptionPlan::class,
            UserSubscription::class,
            'user_id', // Foreign key on user_subscriptions table...
            'id', // Foreign key on subscription_plans table...
            'id', // Local key on users table...
            'subscription_plan_id' // Local key on user_subscriptions table...
        )->where('user_subscriptions.status', 'active')
         ->where(function($query) {
             $query->whereNull('user_subscriptions.ends_at')
                   ->orWhere('user_subscriptions.ends_at', '>', now());
         });
    }

    /**
     * Check if user has an active paid subscription
     */
    public function hasPaidSubscription(): bool
    {
        $subscription = $this->activeSubscription()->first();
        if (!$subscription) {
            return false;
        }

        // Check if subscription is still valid (not expired)
        if ($subscription->ends_at && $subscription->ends_at < now()) {
            return false;
        }

        // Check if it's a paid plan (price > 0)
        $plan = $subscription->subscriptionPlan;
        return $plan && $plan->price_toman > 0;
    }

    /**
     * Check if user can access a specific question number in an exam
     * All users now have unlimited access to all questions
     */
    public function canAccessQuestion(int $questionNumber): bool
    {
        // All users (free, guest, paid) can access all questions
        return true;
    }

    /**
     * Get the user's role status for display.
     */
    public function getRoleStatus(): string
    {
        if ($this->hasRole('admin')) {
            return 'مدیر سیستم';
        }

        $subscription = $this->activeSubscription()->first();

        if (!$subscription) {
            return 'کاربر رایگان';
        }

        $plan = $subscription->subscriptionPlan;

        // Paid subscription
        if ($plan && $plan->price_toman > 0) {
            return 'اشتراک ویژه';
        }

        // Free trial subscription (active)
        if ($plan && $plan->price_toman <= 0 && $subscription->ends_at) {
            return 'اشتراک هدیه';
        }

        // Unlimited free plan (if any)
        if ($plan && $plan->price_toman <= 0 && !$subscription->ends_at) {
            return 'کاربر رایگان';
        }

        return 'کاربر رایگان';
    }
}
