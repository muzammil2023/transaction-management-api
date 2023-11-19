@extends('layout.app')

@section('content')
<h2>Create Transaction</h2>

@if($errors->any())
<div style="color: red;">
    <ul>
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
<div class="container">
    <form method="post" action="{{ url('/transaction/create') }}">
        @csrf

        <div>
            <label for="amount">Amount:</label>
            <input type="number" name="amount" id="amount" step="1" required>
        </div>

        <div>
            <label for="due_on">Due On:</label>
            <input type="date" name="due_on" id="due_on" required>
        </div>

        <div>
            <label for="is_vat_inclusive">Is VAT Inclusive:</label>
            <input type="checkbox" name="is_vat_inclusive" id="is_vat_inclusive">
        </div>

        <div>
            <label for="payer">Payer:</label>
            <!-- Replace 'users' with your actual user model -->
            <select name="payer" id="payer" required>
                @foreach($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="vat">VAT (%):</label>
            <input type="number" name="vat" id="vat" step="0.01" required>
        </div>

        <div>
            <label for="paid_on">Paid On:</label>
            <input type="date" name="paid_on" id="paid_on">
        </div>

        <div>
            <label for="paid_amount">Paid Amount:</label>
            <input type="number" name="paid_amount" id="paid_amount" step="0.01">
        </div>

        <div>
            <label for="detail">Detail:</label>
            <textarea name="detail" id="detail"></textarea>
        </div>

        <div>
            <button type="submit">Create Transaction</button>
        </div>
    </form>
</div>

@endsection