<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PaymentService
{
    public function manualPay(int $enrollmentId, float $amount, UploadedFile $proof): Payment
    {
        $path = $proof->store('payments', 'public');
        return Payment::create([
            'enrollment_id'  => $enrollmentId,
            'amount'         => $amount,
            'payment_method' => 'manual',
            'proof_url'      => Storage::url($path),
            'status'         => 'pending',
        ]);
    }

    public function gatewayPay(int $enrollmentId, float $amount): array
    {
        throw new \Exception('Gateway payment not implemented yet.');
    }
}
