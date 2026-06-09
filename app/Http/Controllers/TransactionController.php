<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function create(Request $request): JsonResponse
    {
        $apiKey = $request->bearerToken();

        if (!$apiKey) {
            return response()->json(['error' => 'Missing API key. Use Authorization: Bearer {api_key}'], 401);
        }

        $merchant = Merchant::where('api_key', $apiKey)->first();

        if (!$merchant) {
            return response()->json(['error' => 'Invalid API key'], 401);
        }

        $validated = $request->validate([
            'amount'       => 'required|integer|min:1',
            'currency'     => 'sometimes|string|size:3',
            'redirect_url' => 'required|url',
            'webhook_url'  => 'sometimes|nullable|url',
            'local_id'     => 'sometimes|nullable|string|max:100',
        ]);

        $transaction = Transaction::create([
            'merchant_id'  => $merchant->id,
            'amount'       => $validated['amount'],
            'currency'     => $validated['currency'] ?? 'MVR',
            'redirect_url' => $validated['redirect_url'],
            'webhook_url'  => $validated['webhook_url'] ?? $merchant->webhook_url,
            'local_id'     => $validated['local_id'] ?? null,
            'status'       => 'pending',
        ]);

        return response()->json([
            'transaction_id' => $transaction->id,
            'status'         => 'pending',
            'payment_url'    => secure_url('/pay/' . $transaction->id),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $transaction = Transaction::with('merchant')->find($id);

        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        return response()->json([
            'transaction_id' => $transaction->id,
            'local_id'       => $transaction->local_id,
            'amount'         => $transaction->amount,
            'currency'       => $transaction->currency,
            'status'         => $transaction->status,
            'merchant'       => $transaction->merchant->name,
            'created_at'     => $transaction->created_at,
        ]);
    }
}
