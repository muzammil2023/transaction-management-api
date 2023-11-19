
<header>
    <h1>Transaction Management</h1>
    <nav>
        <a href="{{ url('/transaction') }}">Transactions</a>
        @if(auth()->user()->isAdmin())
        <a href="{{ url('/transaction/create') }}">Add Transaction</a>
        <a href="{{ url('/add-user') }}">Add User</a>
        <a href="{{ url('/transaction/report') }}">Report</a>
        @endif
        <a href="{{ url('/logout') }}">Logout</a>
    </nav>
</header>