<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class($appearance ?? '')>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="view-transition" content="same-origin">

    <meta property="og:title" content="@yield('title')"/>
    <meta property="og:site_name" content="Grafikart.fr"/>
    <meta property="og:language" content="fr"/>
    <meta name="twitter:author" content="@grafikart_fr"/>
    @yield('head')
    <link rel="alternate" type="application/rss+xml" title="Grafikart.fr | Flux" href="{{ url('rss') }}"/>
    <link rel="apple-touch-icon" sizes="128x128" href="/favicons/icon-128x128.png">

    <title>@yield('title') | {{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=crimson-pro:400,600,700|inter:400,600,700" rel="stylesheet" />
    <link rel="search" type="application/opensearchdescription+xml" title="Grafikart" href="/opensearch.xml">

    @viteReactRefresh
    @vite(['resources/js/front.ts'])
    @if (isset($_SERVER['FRANKENPHP_HOT_RELOAD']))
        <meta name="frankenphp-hot-reload:url" content="{{$_SERVER['FRANKENPHP_HOT_RELOAD']}}">
        <script src="https://cdn.jsdelivr.net/npm/idiomorph"></script>
        <script src="https://cdn.jsdelivr.net/npm/frankenphp-hot-reload/+esm" type="module"></script>
    @endif
</head>
<body class="{{ cn(["font-sans antialiased text-foreground bg-background min-h-screen flex flex-col", $class ?? '']) }}">
@if (session('success'))
    <x-atoms.toast type="success" :message="session('success')" />
@endif
@if (session('error'))
    <x-atoms.toast type="error" :message="session('error')" />
@endif
<x-organisms.header />
@yield('body')
<x-organisms.footer />
<search-input></search-input>
</body>
</html>
