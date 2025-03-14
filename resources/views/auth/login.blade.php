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

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

</head>


<body>
    <header>
        <nav class="navbar navbar-expand-lg bg-light">
            <div class="container d-flex justify-content-center">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img src="{{ asset('img/potal_site.png') }}" height="50">
                </a>
            </div>
        </nav>
    </header>
    <main>
        <div class="container pt-5">
            <div class="row justify-content-center">
                <div class="col-md-4">
                    <h1 class="mb-3">ログイン</h3>

                        @if ($errors->has('employee_number'))
                            <div class="error">{{ $errors->first('employee_number') }}</div>
                        @endif

                        <hr class="mb-4">

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <!-- employee_number -->
                            <div class="form-group mb-3">
                                <label>社員番号</label>

                                <input id="employee_number" type="text"
                                    class="form-control @error('employee_number') is-invalid @enderror login-input"
                                    name="employee_number" value="{{ old('employee_number') }}" required

                            </div>

                            <!-- Password -->
                            <div class="form-group mb-3">
                                <label>パスワード</label>

                                <input id="password" type="password"
                                    class="form-control @error('password') is-invalid @enderror login-input"
                                    name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>社員番号又はパスワードが正しくない可能性があります。</strong>
                                    </span>
                                @enderror

                            </div>

                            <!-- Remember Me -->
                            <div class="form-group mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                        {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label samuraimart-check-label w-100" for="remember">
                                        次回から自動的にログインする
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn samuraimart-submit-button w-100 text-white mb-4">
                                ログイン
                            </button>

                        </form>

                </div>
            </div>
        </div>
</body>
</main>

</html>
