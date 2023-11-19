@extends('layout.app')

@section('content')
<h2>Transaction Report</h2>
<div class="container">
    <form method="get" action="{{ url('/transaction/report') }}">
        <label for="start_date">Start Date:</label>
        <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}">

        <label for="end_date">End Date:</label>
        <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}">

        <button type="submit">Generate Report</button>
    </form>
    @if($data->count() > 0)
    <p>Report for the period from {{ $data->first()->year }}-{{ $data->first()->month }} to {{ $data->last()->year }}-{{ $data->last()->month }}</p>
    <table border="1">
        <thead>
            <tr>
                <th>Month</th>
                <th>Year</th>
                <th>Paid</th>
                <th>Outstanding</th>
                <th>Overdue</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $report)
            <tr>
                <td>{{ $report->month }}</td>
                <td>{{ $report->year }}</td>
                <td>{{ $report->paid }}</td>
                <td>{{ $report->outstanding }}</td>
                <td>{{ $report->overdue }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $data->links() }} <!-- Render pagination links -->
    @else
    <p>No data found for the selected date range.</p>
    @endif

</div>

@endsection