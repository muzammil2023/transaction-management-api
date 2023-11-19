<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your App Title</title>
    <!-- add css links -->
    <link rel="stylesheet" href="{{ asset('main.css') }}">
</head>

<body>

    @include('layout.header') <!-- Include the header partial -->

    <div id="app">
        @yield('content') <!-- This is where the content of child views will be injected -->
    </div>


</body>

</html>