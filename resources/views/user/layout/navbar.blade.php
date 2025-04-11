<!-- Page Preloder -->
<div id="preloder">
    <div class="loader"></div>
</div>

<!-- Offcanvas Menu Begin -->
@guest
    <div class="offcanvas-menu-overlay"></div>
    <div class="offcanvas-menu-wrapper">
        <div class="offcanvas__close">+</div>
        <ul class="offcanvas__widget">
            <li><span class="icon_search search-switch"></span></li>
            <li><a href="#"><span class="icon_heart_alt"></span></a></li>
            <li><a href="#"><span class="icon_bag_alt"></span></a></li>
        </ul>
        <div class="offcanvas__logo">
            <a href="{{ url('/') }}"><img src="{{ asset('assets/img/logo.png') }}" alt=""></a>
        </div>
        <div id="mobile-menu-wrap"></div>
        <div class="offcanvas__auth">
            <a href="{{ route('login') }}">Login</a>
            <a href="{{ route('register') }}">Register</a>
        </div>
    </div>
@endguest
<!-- Offcanvas Menu End -->

<!-- Normal Navbar Begin -->
<div class="container">
    <div class="row">
        <div class="col-lg-3 col-md-3">
            <div class="header__logo">
                <a href="{{ url('/') }}"><img src="{{ asset('assets/img/logo.png') }}" alt=""></a>
            </div>
        </div>
        <div class="col-lg-6 col-md-6">
            <nav class="header__menu mobile-menu">
                <ul>
                    <li class="{{ request()->routeIs('home') ? 'active' : '' }}"><a href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="{{ request()->routeIs('shop') ? 'active' : '' }}"><a href="{{ route('shop') }}">Shop</a>
                    </li>
                    <li><a href="./blog.html">Blog</a></li>
                    <li><a href="./contact.html">Contacts</a></li>
                </ul>
            </nav>
        </div>
        <div class="col-lg-3 col-md-3">
            <div class="header__nav__option">
                <a href="#" class="search-switch"><img src="{{ asset('assets/img/icon/search.png') }}"
                        alt=""></a>
                <a href="#"><img src="{{ asset('assets/img/icon/heart.png') }}" alt=""></a>
                <a href="{{ route('keranjang.index') }}"><img src="{{ asset('assets/img/icon/cart.png') }}"
                        alt="">
                    <span>{{ \App\Models\Keranjang::where('pembeli_id', auth()->id())->count() }}</span></a>
                @auth
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                    <a href="{{ route('logout') }}" class="btn btn-sm btn-danger"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Logout
                    </a>
                @endauth
            </div>
        </div>
    </div>
    <div class="canvas__open"><i class="fa fa-bars"></i></div>
</div>
<!-- Normal Navbar End -->
