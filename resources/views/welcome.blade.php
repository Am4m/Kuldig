<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Sora:wght@100..800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans bg-[#FDFDFC] text-[#1b1b18] flex min-h-screen flex-col">
        <header class="w-full text-sm mb-6 not-has-[nav]:hidden">
            @if (Route::has('login'))
                <nav class="bg-[#03045E] h-32 flex items-center justify-between gap-4 px-10">
                    <h1 class="text-[#FDFDFC] font-bold text-5xl">KuliDigital</h1>
                    <div>
                            @auth
                            <a
                                href="{{ url('/dashboard') }}"
                                class="text-xl leading-normal font-semibold inline-block px-5 py-1.5 border border-[#62605b] hover:border-[#FDFDFC] text-[#FDFDFC] *:rounded-sm text-sm leading-normal text-bold"
                            >
                                Dashboard
                            </a>
                        @else
                            <a
                                href="{{ route('login') }}"
                                class="relative inline-block mx-5 py-2 text-[#FDFDFC] text-xl leading-normal font-semibold transition-colors duration-300 before:absolute before:bottom-0 before:left-0 before:w-0 before:h-[2px] before:bg-[#FDFDFC]  before:transition-all before:duration-300 hover:before:w-full before:content-['']"
                            >
                                Log in
                            </a>


                            @if (Route::has('register'))
                                <a
                                    href="{{ route('register') }}"
                                    class="relative inline-block mx-5 py-2 text-[#FDFDFC] text-xl leading-normal font-semibold transition-colors duration-300 before:absolute before:bottom-0 before:left-0 before:w-0 before:h-[2px] before:bg-[#FDFDFC]  before:transition-all before:duration-300 hover:before:w-full before:content-['']"
                                >
                                    Register
                                </a>
                            @endif
                        @endauth
                    </div>
                </nav>
            @endif
        </header>
        <main class="flex mx-20 my-36">
            <div class="my-20">
                <div class="w-full text-7xl font-bold leading-snug">
                    <h1>Menuju tak terbatas</h1>
                    <h1>bersama Kuli Digital</h1>
                </div>
                <p class="my-5 text-4xl font-medium w-10/12">Bergabunglah bersama kami di Kuli Digital untuk mendapatkan solusi terpadu pengelolaan proyek IT yang efisien dan terorganisir.</p>
            </div>
            <img class="w-1/2" src="{{ asset('images/Company-cuate.svg')}}" alt="">
        </main>
        @if (Route::has('login'))
            <div class="h-14.5 hidden lg:block"></div>
        @endif
    </body>
</html>
