<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReceiptMail;

class PdfReceiptController extends Controller
{
    public function sendReceipt(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        // Hardcoded receipt data for learning
        $receipt = (object) [
            'id' => '12345',
            'customer_name' => 'John Doe',
            'description' => 'Web Development Services',
            'amount' => 250.00,
            'created_at' => now()
        ];

        // Generate PDF from Blade template
        $pdf = PDF::loadView('receipts.pdf', compact('receipt'));

        // Send email
        Mail::to($request->email)->send(new ReceiptMail($receipt, $pdf));

        return response()->json([
            'success' => true,
            'message' => 'Receipt sent successfully to ' . $request->email
        ]);
    }
}
