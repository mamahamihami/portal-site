<header>
    <nav class="navbar navbar-expand-md navbar-light shadow-sm samuraimart-header-container h-auto bg-light">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="{{ asset('img/potal_site.png') }}">
            </a>

            {{-- レスポンシブデザイン　ハンバーガーボタン --}}
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>


            {{-- LaravelのBladeテンプレート で使われる 認証関連のディレクティブ  --}}

            <ul class="navbar-nav ms-auto">
                <li class="nav-item me-4">
                    <h2 class="nav-link fw-bold mb-0"> {{ Auth::user()->name }} さん</h2>
                </li>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    @auth
                        <li class="nav-item me-4">
                            <a class="nav-link fw-bold" href="{{ route('edit_password') }}">パスワード変更
                            </a>
                        </li>

                        <li class="nav-item me-4">
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                            <a class="nav-link fw-bold" href="{{route('login')}}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                ログアウト
                            </a>
                        </li>
                    @endauth
                </div>
            </ul>

        </div>
    </nav>
</header>
