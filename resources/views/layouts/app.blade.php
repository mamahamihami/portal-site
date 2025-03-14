<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    {{-- Font Awesome --}}
    <script src="https://kit.fontawesome.com/9176734696.js" crossorigin="anonymous"></script>


    {{-- JavaScript（jQuery使用） --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

</head>

<body>
    <div id="app" class="samuraimart-wrapper">
        @component('components.header')
        @endcomponent

        <main class="py-1">
            @yield('content')
        </main>

        @component('components.footer')
        @endcomponent
    </div>

    <!-- Scripts -->
    <script src="https://kit.fontawesome.com/9176734696.js" crossorigin="anonymous"></script>

</body>

</html>
