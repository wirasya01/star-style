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
                <a href="{{ url('/') }}" class="logo-animate">
                    <img src="{{ asset('assets/img/logo.png') }}" alt="" class="logo-hover">
                </a>
            </div>
        </div>
        <div class="col-lg-6 col-md-6">
            <nav class="header__menu mobile-menu">
                <ul>
                    <li class="{{ request()->routeIs('home') ? 'active' : '' }}">
                        <a href="{{ route('home') }}" class="nav-link">
                            <span class="link-text">Home</span>
                            <span class="link-underline"></span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('shop') ? 'active' : '' }}">
                        <a href="{{ route('shop') }}" class="nav-link">
                            <span class="link-text">Shop</span>
                            <span class="link-underline"></span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pesanan.index') }}" class="nav-link">
                            <span class="link-text">Pesanan</span>
                            <span class="link-underline"></span>
                        </a>
                    </li>
                    <li>
                        <a href="./contact.html" class="nav-link">
                            <span class="link-text">Contacts</span>
                            <span class="link-underline"></span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        <div class="col-lg-3 col-md-3">
            <div class="header__nav__option">
                <a href="#" class="search-switch icon-animate">
                    <img src="{{ asset('assets/img/icon/search.png') }}" alt="">
                </a>
                <a href="#" class="icon-animate">
                    <img src="{{ asset('assets/img/icon/heart.png') }}" alt="">
                </a>
                <a href="{{ route('keranjang.index') }}" class="cart-animate">
                    <img src="{{ asset('assets/img/icon/cart.png') }}" alt="">
                    <span
                        class="cart-counter">{{ \App\Models\Keranjang::where('pembeli_id', auth()->id())->count() }}</span>
                </a>
                @auth
                    <div class="profile-menu">
                        <div class="profile-toggle" onclick="toggleProfileDropdown()">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=343a40&color=fff&rounded=true" alt="Profile" class="profile-img">

                        </div>
                        <div class="profile-dropdown" id="profileDropdown">
                            <a href="{{ route('profile.show') }}">Profil Saya</a>
                            <a href="{{ route('pesanan.index') }}">Pesanan</a>
                            <a href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Keluar</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </div>
                @endauth

            </div>
        </div>
    </div>
    <div class="canvas__open"><i class="fa fa-bars menu-toggle"></i></div>
</div>
<!-- Normal Navbar End -->
<style>
    .profile-menu {
        position: relative;
        display: inline-block;
        margin-left: 12px;
    }

    .profile-img {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #d10024;
        cursor: pointer;
        transition: transform 0.3s ease;
    }

    .profile-img:hover {
        transform: scale(1.1);
    }

    .profile-dropdown {
        position: absolute;
        top: 48px;
        right: 0;
        background-color: #fff;
        border: 1px solid #eee;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        width: 160px;
        display: none;
        z-index: 999;
    }

    .profile-dropdown a {
        display: block;
        padding: 10px 15px;
        color: #333;
        font-size: 14px;
        text-decoration: none;
        transition: background 0.3s;
    }

    .profile-dropdown a:hover {
        background-color: #f8f8f8;
        color: #d10024;
    }

    .show-dropdown {
        display: block;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
