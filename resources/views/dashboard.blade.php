<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mock Payment Gateway — Dashboard</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f0f4f8;
            color: #333;
        }
        .topbar {
            background: #1a3c6e;
            color: #fff;
            padding: 16px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .topbar h1 { font-size: 18px; font-weight: 600; }
        .badge { background: #f59e0b; color: #000; font-size: 11px; font-weight: 700; padding: 2px 10px; border-radius: 20px; }
        .container { max-width: 960px; margin: 32px auto; padding: 0 20px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 32px; }
        @media(max-width:700px) { .grid { grid-template-columns: 1fr; } }
        .card { background: #fff; border-radius: 10px; box-shadow: 0 2px 12px rgba(0,0,0,0.07); padding: 24px; }
        .card h2 { font-size: 16px; font-weight: 600; margin-bottom: 18px; color: #1a3c6e; }
        label { display: block; font-size: 13px; color: #555; margin-bottom: 4px; margin-top: 12px; }
        input[type=text], input[type=url] {
            width: 100%; padding: 9px 12px; border: 1px solid #ddd;
            border-radius: 6px; font-size: 14px; color: #333;
        }
        input:focus { outline: none; border-color: #1a3c6e; }
        .btn-primary {
            margin-top: 16px; background: #1a3c6e; color: #fff;
            border: none; padding: 10px 20px; border-radius: 6px;
            font-size: 14px; font-weight: 600; cursor: pointer; width: 100%;
        }
        .btn-primary:hover { background: #163060; }
        .merchant-item {
            display: flex; align-items: center; justify-content: space-between;
            padding: 12px 0; border-bottom: 1px solid #f0f0f0;
        }
        .merchant-item:last-child { border-bottom: none; }
        .merchant-info h3 { font-size: 15px; font-weight: 600; }
        .merchant-info p { font-size: 12px; color: #888; margin-top: 2px; }
        .api-key {
            font-family: monospace; font-size: 11px; background: #f4f4f4;
            padding: 4px 8px; border-radius: 4px; color: #333;
            user-select: all; cursor: text; word-break: break-all;
        }
        .delete-btn {
            background: none; border: 1px solid #ddd; color: #dc2626;
            padding: 4px 12px; border-radius: 6px; font-size: 12px; cursor: pointer;
            margin-left: 12px; flex-shrink: 0;
        }
        .delete-btn:hover { background: #fef2f2; }
        .empty { color: #aaa; font-size: 14px; text-align: center; padding: 20px 0; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th { text-align: left; padding: 8px 10px; color: #888; font-weight: 600;
             border-bottom: 2px solid #f0f0f0; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; }
        td { padding: 10px; border-bottom: 1px solid #f8f8f8; }
        tr:last-child td { border-bottom: none; }
        .status {
            display: inline-block; padding: 2px 10px; border-radius: 20px;
            font-size: 11px; font-weight: 700; text-transform: uppercase;
        }
        .status-pending  { background: #fef3c7; color: #92400e; }
        .status-confirmed { background: #d1fae5; color: #065f46; }
        .status-declined  { background: #fee2e2; color: #991b1b; }
        .alert { background: #d1fae5; color: #065f46; padding: 12px 16px; border-radius: 8px; font-size: 14px; margin-bottom: 20px; }
        .full { grid-column: 1 / -1; }
        .api-doc { background: #1e293b; color: #e2e8f0; border-radius: 8px; padding: 16px; margin-top: 16px; font-size: 12px; font-family: monospace; line-height: 1.8; overflow-x: auto; }
        .api-doc .comment { color: #94a3b8; }
        .api-doc .key { color: #7dd3fc; }
        .api-doc .value { color: #86efac; }
    </style>
</head>
<body>

<div class="topbar">
    <h1>Mock Payment Gateway</h1>
    <span class="badge">TEST MODE</span>
</div>

<div class="container">

    @if(session('success'))
    <div class="alert">{{ session('success') }}</div>
    @endif

    <div class="grid">

        {{-- Create Merchant --}}
        <div class="card">
            <h2>Add Merchant (App)</h2>
            <form action="/merchants" method="POST">
                @csrf
                <label>App Name</label>
                <input type="text" name="name" placeholder="e.g. My Shop" required>
                <label>Webhook URL <small style="color:#aaa">(optional)</small></label>
                <input type="url" name="webhook_url" placeholder="https://yourapp.test/webhook">
                <button type="submit" class="btn-primary">Generate API Key</button>
            </form>
        </div>

        {{-- Merchants List --}}
        <div class="card">
            <h2>Merchants & API Keys</h2>
            @if($merchants->isEmpty())
                <div class="empty">No merchants yet. Add one on the left.</div>
            @else
                @foreach($merchants as $merchant)
                <div class="merchant-item">
                    <div class="merchant-info" style="flex:1">
                        <h3>{{ $merchant->name }}</h3>
                        <div class="api-key">{{ $merchant->api_key }}</div>
                        <p>{{ $merchant->transactions_count }} transaction(s)
                            @if($merchant->webhook_url) · webhook set @endif
                        </p>
                    </div>
                    <form action="/merchants/{{ $merchant->id }}" method="POST" onsubmit="return confirm('Delete merchant?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="delete-btn">Delete</button>
                    </form>
                </div>
                @endforeach
            @endif
        </div>

        {{-- API Docs --}}
        <div class="card full">
            <h2>How to Use in Your App</h2>
            <div class="api-doc">
<span class="comment"># Step 1: Create a transaction</span>
POST {{ url('/api/transaction') }}
<span class="key">Authorization:</span> <span class="value">Bearer {your_api_key}</span>
<span class="key">Content-Type:</span> <span class="value">application/json</span>

{
  <span class="key">"amount"</span>:       <span class="value">1500</span>,        <span class="comment">// 1500 = MVR 15.00 (in laari)</span>
  <span class="key">"currency"</span>:     <span class="value">"MVR"</span>,
  <span class="key">"redirect_url"</span>: <span class="value">"https://yourapp.test/payment/callback"</span>,
  <span class="key">"webhook_url"</span>:  <span class="value">"https://yourapp.test/webhook"</span>,  <span class="comment">// optional</span>
  <span class="key">"local_id"</span>:     <span class="value">"order-123"</span>   <span class="comment">// optional, your own order ID</span>
}

<span class="comment"># Response → redirect customer to payment_url</span>
{
  <span class="key">"transaction_id"</span>: <span class="value">"uuid"</span>,
  <span class="key">"status"</span>:         <span class="value">"pending"</span>,
  <span class="key">"payment_url"</span>:    <span class="value">"{{ url('/pay/') }}{uuid}"</span>
}

<span class="comment"># Step 2: Customer lands on payment page → clicks Approve or Decline</span>
<span class="comment"># → Redirected back to your redirect_url with:  ?transaction_id=&status=confirmed&local_id=</span>
<span class="comment"># → Webhook POST sent to webhook_url with signature (HMAC-SHA256 using your api_key)</span>

<span class="comment"># Check transaction status anytime:</span>
GET {{ url('/api/transaction/') }}{transaction_id}
            </div>
        </div>

        {{-- Recent Transactions --}}
        <div class="card full">
            <h2>Recent Transactions</h2>
            @if($transactions->isEmpty())
                <div class="empty">No transactions yet.</div>
            @else
            <table>
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Merchant</th>
                        <th>Amount</th>
                        <th>Local ID</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $tx)
                    <tr>
                        <td style="font-family:monospace;font-size:11px;">{{ $tx->id }}</td>
                        <td>{{ $tx->merchant->name }}</td>
                        <td>{{ $tx->formattedAmount() }}</td>
                        <td>{{ $tx->local_id ?? '—' }}</td>
                        <td><span class="status status-{{ $tx->status }}">{{ $tx->status }}</span></td>
                        <td>{{ $tx->created_at->format('d M Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>

    </div>
</div>

</body>
</html>
