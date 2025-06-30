<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Enrollment;

class EnsurePaymentVerified
{
    public function handle(Request $request, Closure $next)
    {
        // Kita asumsikan route selalu menyertakan {enrollment} parameter
        $enrollmentId = $request->route('enrollment')
            ?? $request->input('enrollment_id');

        if (! $enrollmentId) {
            return response()->json(['message' => 'Enrollment not specified'], 400);
        }

        $enrollment = Enrollment::with('payments')
            ->where('id', $enrollmentId)
            ->where('student_id', $request->user()->id)
            ->first();

        if (! $enrollment) {
            return response()->json(['message' => 'Enrollment not found'], 404);
        }

        // Cek ada payment yang berstatus 'paid'
        $paid = $enrollment->payments()
            ->where('status', 'paid')
            ->exists();

        if (! $paid) {
            return response()->json([
                'message' => 'Access denied. Payment not verified.'
            ], 402);
        }

        // Jika perlu, inject enrollment ke request
        $request->attributes->set('enrollment', $enrollment);

        return $next($request);
    }
}
