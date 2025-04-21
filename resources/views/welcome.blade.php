<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome to Starboy Fashion</title>

    <!-- Font -->
    <link href="https://fonts.bunny.net/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom Styles -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .bg-dark {
            background-color: #000000;
        }
        .text-white-star {
            color: #fff;
        }
        .star-animation {
            animation: starAnimation 2s infinite alternate;
        }
        @keyframes starAnimation {
            0% { transform: scale(1); opacity: 1; }
            100% { transform: scale(1.5); opacity: 0.6; }
        }
    </style>
</head>
<body class="bg-dark text-white-star">

    <div class="flex items-center justify-center min-h-screen px-4 sm:px-6 lg:px-8 relative">

        <div class="w-full max-w-6xl space-y-12 text-center z-10">

            <!-- Logo -->
            <div class="flex justify-center mb-10">
                <!-- Bintang yang memiliki animasi -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-white-star star-animation" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2l2.4 7.8h8.4l-6.8 5 2.6 7.8-6.8-5-6.8 5 2.6-7.8-6.8-5h8.4L12 2z"/>
                </svg>
            </div>

            <!-- Hero Section -->
            <div class="space-y-6">
                <h1 class="text-4xl sm:text-6xl font-bold leading-tight text-white-star">
                    Welcome to StarStyle Fashion
                </h1>
                <p class="text-lg sm:text-2xl text-gray-300 max-w-3xl mx-auto">
                    Discover the latest trends in fashion, exclusively curated for you. Embrace your unique style with our collection of premium apparel.
                </p>
            </div>

            <!-- Auth Buttons -->
            @if (Route::has('login'))
                <div class="text-right mt-8">
                    @auth
                        <a href="{{ url('/home') }}"
                           class="inline-block px-6 py-3 text-sm font-semibold text-white bg-transparent border-2 border-white rounded-xl shadow-lg hover:bg-white hover:text-black transform transition duration-300 hover:scale-105">
                            Home
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="inline-block px-6 py-3 text-sm font-semibold text-white bg-transparent border-2 border-white rounded-xl shadow-lg hover:bg-white hover:text-black transform transition duration-300 hover:scale-105">
                            Log in
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                               class="ml-4 inline-block px-6 py-3 text-sm font-semibold text-white bg-transparent border-2 border-white rounded-xl shadow-lg hover:bg-white hover:text-black transform transition duration-300 hover:scale-105">
                                Register
                            </a>
                        @endif
                    @endauth
                </div>
            @endif

        </div>

        <!-- Star Background Animation -->
        <div class="absolute top-0 left-0 w-full h-full bg-transparent z-0">
            <div class="absolute w-full h-full bg-gradient-to-tl from-gray-900 via-black to-gray-900 opacity-50 star-animation"></div>
        </div>

    </div>

</body>
</html>
