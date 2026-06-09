<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function show(string $id): View|RedirectResponse
    {
        $transaction = Transaction::with('merchant')->find($id);

        if (!$transaction) {
            abort(404, 'Transaction not found.');
        }

        if ($transaction->status !== 'pending') {
            return redirect($transaction->redirect_url . '?' . http_build_query([
                'transaction_id' => $transaction->id,
                'status'         => $transaction->status,
                'local_id'       => $transaction->local_id,
            ]));
        }

        return view('payment', compact('transaction'));
    }

    public function confirm(string $id): RedirectResponse
    {
        $transaction = Transaction::with('merchant')->find($id);

        if (!$transaction || $transaction->status !== 'pending') {
            abort(404);
        }

        $transaction->update(['status' => 'confirmed']);
        $this->sendWebhook($transaction);

        return redirect($transaction->redirect_url . '?' . http_build_query([
            'transaction_id' => $transaction->id,
            'status'         => 'confirmed',
            'local_id'       => $transaction->local_id,
        ]));
    }

    public function decline(string $id): RedirectResponse
    {
        $transaction = Transaction::with('merchant')->find($id);

        if (!$transaction || $transaction->status !== 'pending') {
            abort(404);
        }

        $transaction->update(['status' => 'declined']);
        $this->sendWebhook($transaction);

        return redirect($transaction->redirect_url . '?' . http_build_query([
            'transaction_id' => $transaction->id,
            'status'         => 'declined',
            'local_id'       => $transaction->local_id,
        ]));
    }

    private function sendWebhook(Transaction $transaction): void
    {
        $webhookUrl = $transaction->webhook_url;

        if (!$webhookUrl) {
            return;
        }

        $payload = [
            'transaction_id' => $transaction->id,
            'local_id'       => $transaction->local_id,
            'amount'         => $transaction->amount,
            'currency'       => $transaction->currency,
            'status'         => strtoupper($transaction->status),
            'timestamp'      => now()->toIso8601String(),
        ];

        $signature = hash_hmac('sha256', json_encode($payload), $transaction->merchant->api_key);
        $payload['signature'] = $signature;

        try {
            Http::timeout(5)->post($webhookUrl, $payload);
        } catch (\Exception $e) {
            // Webhook delivery failed silently — merchant can use redirect_url params instead
        }
    }
}
