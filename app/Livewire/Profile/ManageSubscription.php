<?php

namespace App\Livewire\Profile;

use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ManageSubscription extends Component
{
    public function purchaseSubscription($planId)
    {
        // Redirect to payment page instead of directly creating subscription
        return redirect()->route('payment.show', $planId);
    }

    public function render()
    {
        $user = Auth::user();
        $currentSubscription = $user->activeSubscription()->with('subscriptionPlan')->first();
        $availablePlans = SubscriptionPlan::where('is_active', true)
            ->where('price_toman', '>', 0) // Only paid plans
            ->orderBy('price_toman')
            ->get();
        
        return view('livewire.profile.manage-subscription', [
            'currentSubscription' => $currentSubscription,
            'availablePlans' => $availablePlans,
            'hasPaidSubscription' => $user->hasPaidSubscription(),
        ]);
    }
}
