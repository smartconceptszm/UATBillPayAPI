<?php

namespace App\Observers;

use App\Models\Payment;
use App\Models\PaymentAudit;
use Illuminate\Support\Facades\Auth;

class PaymentObserver
{
    public function updated(Payment $payment, string $channel)
    {
        // Don't log if nothing changed
        if (empty($payment->getChanges())) {
            return;
        }

        PaymentAudit::create([
            'payment_id' => $payment->id,
            'oldValues' => $payment->getOriginal(),
            'newValues' => $payment->getChanges(),
            'user_id' => Auth::check() ? Auth::id() : null,
            'updateChannel' => $channel? $channel:null,
            'updated_at' => now(),
        ]);
    }

    public function manualUpdated(Payment $paymentBefore,Payment $paymentAfter, string $channel)
    {
        PaymentAudit::create([
            'payment_id' => $paymentBefore->id,
            'oldValues' => $paymentBefore->getOriginal(),
            'newValues' => $paymentAfter->getChanges(),
            'user_id' => Auth::check() ? Auth::id() : null,
            'updateChannel' => $channel? $channel:null,
            'updated_at' => now(),
        ]);
    }

}