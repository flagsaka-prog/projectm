<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — @yield('title', 'Dashboard')</title>
    @php $logoPath = \App\Models\Setting::get('logo_path'); @endphp
    @if ($logoPath)
        <link rel="icon" href="{{ Storage::url($logoPath) }}?v={{ time() }}" type="image/png">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Hide scrollbar tapi tetap bisa scroll */
        .scrollbar-hide {
            -ms-overflow-style: none;
            /* IE & Edge */
            scrollbar-width: none;
            /* Firefox */
        }

        .scrollbar-hide::-webkit-scrollbar {
            display: none;
            /* Chrome, Safari */
        }
    </style>
</head>

<body class="bg-gray-100 font-exo antialiased">

    {{-- Tambahkan x-data di sini agar state sidebarOpen bisa dipakai sidebar & topbar --}}
    <div x-data="{ sidebarOpen: false }" class="h-screen flex overflow-hidden">

        {{-- Sidebar --}}
        @include('partials.sidebar')

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col overflow-hidden"> {{-- ml-64 agar konten tidak tertutup sidebar di desktop --}}

            {{-- Topbar --}}
            @include('partials.topbar')

            {{-- Page Content --}}
            <main class="flex-1 p-4 md:p-6 overflow-y-auto scrollbar-hide"> {{-- p-4 untuk mobile, p-6 untuk desktop --}}

                {{-- Flash Message --}}
                @if (session('success'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                        class="mb-4 px-4 py-3 bg-green-100 border border-green-300 text-green-800 rounded-lg flex justify-between items-center">
                        <span>{{ session('success') }}</span>
                        <button @click="show = false" class="text-green-600 hover:text-green-900">&times;</button>
                    </div>
                @endif

                @if (session('error'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                        class="mb-4 px-4 py-3 bg-red-100 border border-red-300 text-red-800 rounded-lg flex justify-between items-center">
                        <span>{{ session('error') }}</span>
                        <button @click="show = false" class="text-red-600 hover:text-red-900">&times;</button>
                    </div>
                @endif

                @if (session('info'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                        class="mb-4 px-4 py-3 bg-blue-100 border border-blue-300 text-blue-800 rounded-lg flex justify-between items-center">
                        <span>{{ session('info') }}</span>
                        <button @click="show = false" class="text-blue-600 hover:text-blue-900">&times;</button>
                    </div>
                @endif

                @if (session('warning'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                        class="mb-4 px-4 py-3 bg-yellow-100 border border-yellow-300 text-yellow-800 rounded-lg flex justify-between items-center">
                        <span>{{ session('warning') }}</span>
                        <button @click="show = false" class="text-yellow-600 hover:text-yellow-900">&times;</button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')

</body>

</html>
