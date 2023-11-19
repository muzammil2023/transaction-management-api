@extends('layout.app')

@section('content')
<style>
    .container {
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        margin: auto 10px;
        max-width: 100%;
    }
</style>
<h2>Transaction Index</h2>
<div class="container">
    @if($data->count() > 0)
    <table border="1">
        <thead>
            <tr>
                @if(auth()->user()->isAdmin())
                <th>Action</th>
                @endif
                <th>Name</th>
                <th>Email</th>
                <th>Amount</th>
                <th>VAT</th>
                <th>VAT Type</th>
                <th>Paid Amount</th>
                <th>Due On</th>
                <th>Status</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $transaction)
            <tr>
                @if(auth()->user()->isAdmin())
                <td><a class="btn-primary" href="/transaction/{{ $transaction->id }}/payment"> View Payment</a></td>
                @endif
                <td>{{ $transaction->user->name }}</td>
                <td>{{ $transaction->user->email }}</td>
                <td>{{ $transaction->amount }}</td>
                <td>{{ $transaction->vat }}%</td>
                <td>{{ $transaction->is_vat_inclusive?"Inclusive":"Exclusive" }}</td>
                <td>{{ $transaction->amountPaid() }}</td>
                <td>{{ $transaction->due_on }}</td>
                <td>{{ $transaction->status }}</td>
                <td>{{ $transaction->created_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $data->links() }} <!-- Render pagination links -->

    @else
    <p>No transactions found.</p>
    @endif
</div>

@endsection