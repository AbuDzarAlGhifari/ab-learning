<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Enrollment;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    /**
     * Student: upload bukti bayar → create payment (pending)
     */
    public function store(Request $req, Enrollment $enrollment)
    {
        // Pastikan student hanya untuk enroll miliknya
        if ($req->user()->id !== $enrollment->student_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $req->validate([
            'amount'    => 'required|numeric|min:0',
            'proof'     => 'required|file|mimes:jpg,png,pdf|max:2048',
        ]);

        // Simpan file bukti
        $path = $req->file('proof')->store('payments', 'public');

        $payment = Payment::create([
            'enrollment_id' => $enrollment->id,
            'amount'        => $data['amount'],
            'method'        => 'manual',
            'status'        => 'pending',
            'proof_url'     => Storage::url($path),
        ]);

        return response()->json($payment, 201);
    }

    /**
     * Finance/Admin: list semua pembayaran
     */
    public function index()
    {
        $payments = Payment::with(['enrollment.student', 'confirmer'])
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($payments);
    }

    /**
     * Finance/Admin: confirm pembayaran → status=paid
     */
    public function confirm(Request $req, Payment $payment)
    {
        if ($payment->status !== 'pending') {
            return response()->json(['message' => 'Already processed'], 409);
        }

        $payment->update([
            'status'       => 'paid',
            'confirmed_by' => $req->user()->id,
            'confirmed_at' => now(),
        ]);

        Notification::create([
            'user_id' => $payment->enrollment->student_id,
            'title'   => 'Pembayaran dikonfirmasi',
            'message' => 'Pembayaran kamu untuk kursus ' . $payment->enrollment->course->title . ' telah diterima.',
            'type'    => 'payment'
        ]);

        return response()->json($payment);
    }



    /**
     * Finance/Admin: reject pembayaran → status=rejected
     */
    public function reject(Request $req, Payment $payment)
    {
        if ($payment->status !== 'pending') {
            return response()->json(['message' => 'Already processed'], 409);
        }

        $payment->update([
            'status'       => 'rejected',
            'confirmed_by' => $req->user()->id,
            'confirmed_at' => now(),
        ]);

        Notification::create([
            'user_id' => $payment->enrollment->student_id,
            'title' => 'Pembayaran ditolak',
            'message' => 'Pembayaran kamu untuk kursus ' . $payment->enrollment->course->title . ' ditolak. Silakan periksa kembali bukti bayar.',
            'type' => 'payment'
        ]);


        return response()->json($payment);
    }

    /**
     * Student: lihat riwayat pembayaran miliknya
     */
    public function myPayments(Request $req)
    {
        $payments = $req->user()->enrollments()
            ->with('payments')
            ->get()
            ->pluck('payments')
            ->flatten();
        return response()->json($payments);
    }
}
