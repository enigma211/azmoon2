<?php

namespace App\Livewire;

use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProfilePage extends Component
{
    public $subscription;
    public $availablePlans;
    public $daysRemaining = null;
    public $isExpired = false;

    public function mount()
    {
        $user = Auth::user();
        $this->subscription = $user->activeSubscription;

        // Calculate days remaining
        if ($this->subscription && $this->subscription->ends_at) {
            $this->daysRemaining = now()->diffInDays($this->subscription->ends_at, false);
            $this->isExpired = $this->daysRemaining <= 0;
        }

        // Get available paid plans for upgrade
        $this->availablePlans = SubscriptionPlan::where('price_toman', '>', 0)
            ->where('is_active', true)
            ->get();
    }

    public function render()
    {
        return view('livewire.profile-page')
            ->layout('layouts.app');
    }
}
