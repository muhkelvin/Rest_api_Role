<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\User;
use App\Http\Resources\PaymentResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function index()
    {
        // Dapatkan user yang sedang login
        $user = Auth::user();

        // Ambil pembayaran yang terkait dengan user yang sedang login
        $payments = Payment::where('user_id', $user->id)->paginate(10);

        // Kembalikan koleksi PaymentResource
        return PaymentResource::collection($payments);
    }

    public function store(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        try {
            // Dapatkan user yang sedang login
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'message' => 'User not authenticated',
                ], 401);
            }

            // Buat payment dengan user_id dari user yang sedang login
            $payment = Payment::create([
                'user_id' => $user->id,
                'book_id' => $request->book_id,
            ]);

            return response()->json([
                'message' => 'Payment created successfully',
                'payment' => new PaymentResource($payment),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create payment',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function show(Payment $payment)
    {
        return new PaymentResource($payment);
    }

    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'book_id' => 'nullable|exists:books,id',
            'status' => 'nullable|boolean',
        ]);

        try {
            $payment->update($request->all());

            // Ubah role user jika payment status true
//            $user = User::find($payment->user_id);
//            if ($payment->status) {
//                $user->syncRoles('user payment');
//            }

            return response()->json([
                'message' => 'Payment updated successfully',
                'payment' => new PaymentResource($payment),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update payment',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function destroy(Payment $payment)
    {
        try {
            $payment->delete();

            return response()->json([
                'message' => 'Payment deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete payment',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function verify(Request $request, Payment $payment)
    {
        Log::info('Request received', $request->all());

        $request->validate([
            'proof_of_payment' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        try {
            if ($request->hasFile('proof_of_payment')) {
                Log::info('File found in request');
                $file = $request->file('proof_of_payment');
                $path = $file->store('proof_of_payments', 'public');

                $payment->update([
                    'status' => 'awaiting_confirmation',
                    'proof_of_payment_path' => $path,
                ]);

                return response()->json([
                    'message' => 'Payment proof submitted successfully, awaiting confirmation',
                    'payment' => new PaymentResource($payment),
                ], 200);
            }

            Log::warning('No file found in request');
            return response()->json([
                'message' => 'Proof of payment is required',
            ], 400);
        } catch (\Exception $e) {
            Log::error('Failed to submit payment proof', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Failed to submit payment proof',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function confirm(Request $request, Payment $payment)
    {
        Log::info('Confirm payment called', ['payment_id' => $payment->id]);

        try {
            $payment->update([
                'status' => 'confirmed',
            ]);

            return response()->json([
                'message' => 'Payment status updated to confirmed',
                'payment' => new PaymentResource($payment),
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to confirm payment', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Failed to confirm payment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
