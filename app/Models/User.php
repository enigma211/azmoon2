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
            ->latest('starts_at');
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
     * Free users: limited to first 4 questions
     * Paid subscribers: unlimited access
     */
    public function canAccessQuestion(int $questionNumber): bool
    {
        // Paid subscribers have unlimited access
        if ($this->hasPaidSubscription()) {
            return true;
        }

        // Free users can only access first 4 questions (index 0-3)
        return $questionNumber < 4;
    }
}
