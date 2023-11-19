@extends('layout.app')

@section('content')
<div class="container">
    <h2>Transaction Detail</h2>
    <p><strong>Amount:</strong> {{ $transaction->amount }}</p>
    <p><strong>Due On:</strong> {{ $transaction->due_on }}</p>
    <p><strong>VAT:</strong> {{ $transaction->vat }}</p>
    <p><strong>Status:</strong> {{ $transaction->status }}</p>

    <h3>Payment history:</h3>
    @if ($transaction->payments->count() > 0)
    <ul>
        @foreach ($transaction->payments as $payment)
        <li>
            <strong>Amount:</strong> {{ $payment->amount }}
            <br>
            <strong>Paid On:</strong> {{ $payment->paid_on }}
            <br>
            <strong>Details:</strong> {{ $payment->details ?? 'N/A' }}
        </li>
        @endforeach
    </ul>
    @else
    <p>No payments recorded for this transaction.</p>
    @endif
    <h2>Record Payment</h2>
    @if ($errors->any())
    <div style="color: red;">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <form method="post" action="{{ url("/transaction/{$transaction->id}/payment") }}">
        @csrf
        <div>
            <label for="amount">Amount:</label>
            <input type="number" name="amount" id="amount" value="{{ old('amount') }}" required>
        </div>
        <div>
            <label for="paid_on">Paid On:</label>
            <input type="date" name="paid_on" id="paid_on" value="{{ old('paid_on') }}" required>
        </div>
        <div>
            <label for="details">Details:</label>
        </div>
        <div>
            <textarea name="details" id="details">{{ old('details') }}</textarea>
        </div>

        <button type="submit">Record Payment</button>
    </form>
</div>

@endsection