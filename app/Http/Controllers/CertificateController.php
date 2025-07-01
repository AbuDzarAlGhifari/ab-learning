<?php

namespace App\Http\Controllers;

use App\Models\QuizAttempt;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function generate(Request $req, QuizAttempt $attempt)
    {
        if ($attempt->student_id !== $req->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = [
            'student' => $req->user(),
            'course'  => $attempt->quiz->course,
            'quiz'    => $attempt->quiz,
            'score'   => $attempt->score,
            'date'    => now()->format('d M Y'),
        ];

        $pdf = PDF::loadView('certificates.template', $data);
        return $pdf->download('Certificate_' . $attempt->quiz->title . '.pdf');
    }
}
