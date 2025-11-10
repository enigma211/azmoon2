<?php

namespace App\Livewire;

use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProfilePage extends Component
{
    public $subscription;
    public $availablePlans;
    public $daysRemaining = null;
    public $isExpired = false;
    public $isGuest = false;

    public function mount()
    {
        if (!Auth::check()) {
            $this->isGuest = true;
            return;
        }

        $user = Auth::user();
        $activeSubscription = $user->activeSubscription()->first();

        if ($activeSubscription) {
            $this->subscription = $activeSubscription->subscriptionPlan;
            if ($activeSubscription->ends_at) {
                $this->daysRemaining = now()->diffInDays($activeSubscription->ends_at, false);
                $this->isExpired = $this->daysRemaining <= 0;
            } else {
                $this->daysRemaining = null; // Unlimited
            }
        } else {
            $this->subscription = null;
            $this->isExpired = true;
        }

        $this->availablePlans = SubscriptionPlan::where('price_toman', '>', 0)
            ->where('is_active', true)
            ->orderBy('price_toman', 'asc')
            ->get();
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        
        return redirect()->route('home');
    }

    public function render()
    {
        return view('livewire.profile-page')
            ->layout('layouts.app');
    }
}
