
<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="monetag" content="ef4c401c621a8990f2356be856682f44">
    <meta name="monetag" content="ef4c401c621a8990f2356be856682f44">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

   {{-- SEO TITLE --}}
<title>@yield('title', 'ENTERTAINMENT+ | Anime, Movies & TV Shows')</title>

{{-- SEO DESCRIPTION --}}
<meta name="description" content="@yield('description', 'Discover anime, movies and TV shows on ENTERTAINMENT+.')">

{{-- ROBOTS --}}
<meta name="robots" content="index, follow, max-image-preview:large">

{{-- CANONICAL URL --}}
<link rel="canonical" href="{{ url()->current() }}">

{{-- OPEN GRAPH --}}
<meta property="og:type" content="website">
<meta property="og:site_name" content="ENTERTAINMENT+">
<meta property="og:title" content="@yield('title', 'ENTERTAINMENT+ | Anime, Movies & TV Shows')">
<meta property="og:description" content="@yield('description', 'Discover anime, movies and TV shows on ENTERTAINMENT+.')">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:image" content="@yield('og_image', asset('images/entertainment-cover.jpg'))">

{{-- TWITTER --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="@yield('title', 'ENTERTAINMENT+')">
<meta name="twitter:description" content="@yield('description', 'Discover anime, movies and TV shows.')">
<meta name="twitter:image" content="@yield('og_image', asset('images/entertainment-cover.jpg'))">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body class="min-h-screen bg-[#09090f] text-white antialiased">

    @include('partials.navbar')

    <main class="min-h-[70vh]">
        @yield('content')
    </main>

    @include('partials.footer')

    @stack('scripts')

</body>
</html>
