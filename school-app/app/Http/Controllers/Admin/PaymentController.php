<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Payment, PaymentVerification};

class PaymentController extends Controller
{
    public function verify(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);
        PaymentVerification::updateOrCreate(
            ['payment_id' => $payment->id],
            [
                'verified_by' => auth()->id(),
                'verified_at' => now(),
                'status' => 'verified',
                'note' => $request->input('note')
            ]
        );
        event(new \App\Events\PaymentVerified($payment));
        return response()->json(['status'=>'verified']);
    }
}

