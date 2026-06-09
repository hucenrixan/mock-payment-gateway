<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Payment</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f0f4f8;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.10);
            width: 100%;
            max-width: 420px;
            overflow: hidden;
        }
        .header {
            background: #1a3c6e;
            color: #fff;
            padding: 24px;
            text-align: center;
        }
        .header .bank-name {
            font-size: 13px;
            letter-spacing: 2px;
            text-transform: uppercase;
            opacity: 0.8;
            margin-bottom: 4px;
        }
        .header h1 { font-size: 20px; font-weight: 600; }
        .test-badge {
            display: inline-block;
            background: #f59e0b;
            color: #000;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 10px;
            border-radius: 20px;
            margin-top: 8px;
            letter-spacing: 1px;
        }
        .body { padding: 28px; }
        .merchant-name {
            text-align: center;
            font-size: 15px;
            color: #555;
            margin-bottom: 4px;
        }
        .amount-display {
            text-align: center;
            font-size: 38px;
            font-weight: 700;
            color: #1a3c6e;
            margin: 16px 0;
        }
        .amount-display span { font-size: 18px; font-weight: 400; color: #888; }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
            font-size: 14px;
        }
        .info-row:last-of-type { border-bottom: none; }
        .info-row .label { color: #888; }
        .info-row .value { color: #333; font-weight: 500; }
        .divider { border: none; border-top: 1px solid #eee; margin: 20px 0; }
        .sim-label {
            text-align: center;
            font-size: 12px;
            color: #aaa;
            margin-bottom: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .btn {
            display: block;
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-bottom: 10px;
            transition: opacity 0.2s;
        }
        .btn:hover { opacity: 0.9; }
        .btn-confirm { background: #16a34a; color: #fff; }
        .btn-decline { background: #fff; color: #dc2626; border: 2px solid #dc2626; }
        .footer {
            background: #f8fafc;
            padding: 14px;
            text-align: center;
            font-size: 12px;
            color: #aaa;
            border-top: 1px solid #eee;
        }
        .lock-icon { margin-right: 4px; }
    </style>
</head>
<body>
<div class="card">
    <div class="header">
        <div class="bank-name">Mock Payment Gateway</div>
        <h1>Secure Checkout</h1>
        <div class="test-badge">TEST MODE</div>
    </div>
    <div class="body">
        <div class="merchant-name">Payment to</div>
        <div class="amount-display">
            {{ number_format($transaction->amount / 100, 2) }}
            <span>{{ $transaction->currency }}</span>
        </div>

        <div class="info-row">
            <span class="label">Merchant</span>
            <span class="value">{{ $transaction->merchant->name }}</span>
        </div>
        @if($transaction->local_id)
        <div class="info-row">
            <span class="label">Order ID</span>
            <span class="value">{{ $transaction->local_id }}</span>
        </div>
        @endif
        <div class="info-row">
            <span class="label">Transaction ID</span>
            <span class="value" style="font-size:12px;">{{ $transaction->id }}</span>
        </div>

        <hr class="divider">

        <div class="sim-label">Simulate Payment Result</div>

        <form action="/pay/{{ $transaction->id }}/confirm" method="POST">
            @csrf
            <button type="submit" class="btn btn-confirm">✓ Approve Payment</button>
        </form>

        <form action="/pay/{{ $transaction->id }}/decline" method="POST">
            @csrf
            <button type="submit" class="btn btn-decline">✗ Decline Payment</button>
        </form>
    </div>
    <div class="footer">
        <span class="lock-icon">🔒</span> This is a test gateway. No real money is processed.
    </div>
</div>
</body>
</html>
