<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.css" rel="stylesheet" />
    @livewireStyles
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @wireUiScripts
    {{-- <script src="//unpkg.com/alpinejs" defer></script> --}}
</head>

<body class=" bg-[#F6F6F6] font-sans antialiased" x-data="{ sidebarOpen: true }">
    <x-sidebar></x-sidebar>
    <x-dialog />
    {{-- <x-notifications /> --}}
    <div class="p-4 transition-all sm:ml-64" :class="{ 'sm:!ml-28': !sidebarOpen }">
        <livewire:layout.navigation />
        <div class="p-4">
            <!-- Page Heading -->
            @if (isset($header))
                <header class="">
                    <div class="w-full">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </div>
    @livewireScripts
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>
</body>
</html>
