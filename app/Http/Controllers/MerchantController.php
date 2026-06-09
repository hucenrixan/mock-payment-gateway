<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MerchantController extends Controller
{
    public function index(): View
    {
        $merchants = Merchant::withCount('transactions')->latest()->get();
        $transactions = Transaction::with('merchant')->latest()->take(20)->get();

        return view('dashboard', compact('merchants', 'transactions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'webhook_url' => 'nullable|url',
        ]);

        Merchant::create([
            'name'        => $validated['name'],
            'api_key'     => Merchant::generateApiKey(),
            'webhook_url' => $validated['webhook_url'] ?? null,
        ]);

        return redirect('/')->with('success', 'Merchant created successfully.');
    }

    public function destroy(Merchant $merchant): RedirectResponse
    {
        $merchant->delete();

        return redirect('/')->with('success', 'Merchant deleted.');
    }
}
