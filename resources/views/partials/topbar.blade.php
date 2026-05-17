<header class="bg-white border-b border-gray-200 px-4 md:px-6 py-3 flex items-center justify-between sticky top-0 z-10">

    <div class="flex items-center">
        {{-- Tombol Hamburger (Hanya muncul di Mobile/Tablet) --}}
        <button @click="sidebarOpen = !sidebarOpen"
            class="md:hidden mr-4 text-gray-600 hover:text-gray-900 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
            </svg>
        </button>

        {{-- Breadcrumb / Page Title --}}
        <h2 class="text-sm font-semibold text-gray-700">@yield('page-title', 'Dashboard')</h2>
    </div>

    {{-- Right: Online Status + User --}}
    <div class="flex items-center gap-4">

        {{-- Online badge --}}
        <span class="hidden sm:flex items-center gap-1 text-xs text-green-600">
            <span class="w-2 h-2 bg-green-500 rounded-full inline-block"></span>
            Online
        </span>

        {{-- User info --}}
        <div class="text-sm text-gray-600 font-medium">
            {{ auth()->user()->name }}
        </div>

    </div>
</header>
