<style>
    /* MOBILE */
    @media (max-width: 767px) {
        .sidebar-scroll::-webkit-scrollbar {
            display: none;
        }

        .sidebar-scroll {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    }

    /* DESKTOP */
    @media (min-width: 768px) {

        .sidebar-scroll {
            overflow-y: auto;
        }

        /* Chrome, Edge, Safari */
        .sidebar-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.12);
            border-radius: 9999px;
        }

        aside:hover .sidebar-scroll::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.28);
        }

        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.45);
        }

        /* Firefox */
        .sidebar-scroll {
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.15) transparent;
        }

        aside:hover .sidebar-scroll {
            scrollbar-color: rgba(255, 255, 255, 0.3) transparent;
        }
    }
</style>


{{-- Overlay Gelap (Hanya muncul saat sidebar open di mobile) --}}
<div x-show="sidebarOpen" @click="sidebarOpen = false"
    class="fixed inset-0 bg-black/50 z-20 md:hidden transition-opacity duration-300"
    x-transition:enter="ease-in-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in-out duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
    style="display: none;"></div>


<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-30 w-64 bg-gray-900 text-white flex flex-col
           h-screen overflow-hidden
           transform transition-transform duration-300 ease-in-out
           md:translate-x-0 md:static md:z-auto md:shrink-0">

    <img id="background" class="fixed inset-0 w-full h-full object-cover -z-10 brightness-50"
        src="{{ asset('image/background.jpg') }}" alt="bg" />

    {{-- Logo --}}
    <div class="px-6 py-5 border-b border-gray-500 flex items-center justify-between">
        <div class="flex items-center gap-3">
            @php $logoPath = \App\Models\Setting::get('logo_path'); @endphp
            @if ($logoPath)
                <img src="{{ Storage::url($logoPath) }}?v={{ time() }}" alt="Logo" class="h-8 w-auto">
            @else
                <span class="text-2xl">📊</span>
            @endif

            <h1 class="text-lg font-bold tracking-wide">
                {{ \App\Models\Setting::get('app_name', 'Project Manager') }}
            </h1>
        </div>

        {{-- Tombol Close Sidebar (Hanya di mobile) --}}
        <button @click="sidebarOpen = false" class="md:hidden text-gray-400 hover:text-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                </path>
            </svg>
        </button>
    </div>

    <p class="px-6 text-xs text-gray-400 mt-1 pb-2">{{ auth()->user()->roles->first()->name }}</p>

    {{-- Navigation --}}
    <nav id="sidebar-nav" class="sidebar-scroll flex-1 overflow-y-auto px-4 py-4 space-y-1" style="opacity:1">

        {{-- Executive --}}
        @if (auth()->user()->hasRole('CEO'))
            <a href="{{ route('ceo.dashboard') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm hover:bg-gray-700 {{ request()->routeIs('ceo.dashboard') ? 'bg-gray-700' : '' }}">🏠
                Dashboard</a>
        @endif

        @if (auth()->user()->hasRole('COO'))
            <a href="{{ route('coo.dashboard') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm hover:bg-gray-700 {{ request()->routeIs('coo.dashboard') ? 'bg-gray-700' : '' }}">🏠
                Dashboard</a>
        @endif

        @if (auth()->user()->hasRole('CTO'))
            <a href="{{ route('cto.dashboard') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm hover:bg-gray-700 {{ request()->routeIs('cto.dashboard') ? 'bg-gray-700' : '' }}">🏠
                Dashboard</a>
        @endif

        @if (auth()->user()->hasRole('CFO'))
            <a href="{{ route('cfo.dashboard') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm hover:bg-gray-700 {{ request()->routeIs('cfo.dashboard') ? 'bg-gray-700' : '' }}">🏠
                Dashboard</a>
        @endif

        @if (auth()->user()->hasRole('VP'))
            <a href="{{ route('vp.dashboard') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm hover:bg-gray-700 {{ request()->routeIs('vp.dashboard') ? 'bg-gray-700' : '' }}">🏠
                Dashboard</a>
        @endif

        @if (auth()->user()->hasRole('PM'))
            <a href="{{ route('pm.dashboard') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm hover:bg-gray-700 {{ request()->routeIs('pm.dashboard') ? 'bg-gray-700' : '' }}">🏠
                Dashboard</a>
        @endif

        @if (auth()->user()->hasRole('Team Lead'))
            <a href="{{ route('team-lead.dashboard') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm hover:bg-gray-700 {{ request()->routeIs('team-lead.dashboard') ? 'bg-gray-700' : '' }}">🏠
                Dashboard</a>
        @endif

        @if (auth()->user()->hasRole('Developer'))
            <a href="{{ route('developer.dashboard') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm hover:bg-gray-700 {{ request()->routeIs('developer.dashboard') ? 'bg-gray-700' : '' }}">🏠
                Dashboard</a>
        @endif

        @if (auth()->user()->hasRole('Programmer'))
            <a href="{{ route('programmer.dashboard') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm hover:bg-gray-700 {{ request()->routeIs('programmer.dashboard') ? 'bg-gray-700' : '' }}">🏠
                Dashboard</a>
        @endif


        <a href="{{ route('profile.edit') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm hover:bg-gray-700 {{ request()->routeIs('profile.*') ? 'bg-gray-700' : '' }}">👤
            Profil Saya</a>

        @can('notification.view')
            <a href="{{ route('notifications.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm hover:bg-gray-700 {{ request()->routeIs('notifications.*') ? 'bg-gray-700' : '' }}">🔔
                Notifikasi
                @php
                    $unread = \App\Models\Notification::where('user_id', auth()->id())
                        ->whereNull('read_at')
                        ->count();
                @endphp
                @if ($unread > 0)
                    <span class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $unread }}</span>
                @endif
            </a>
        @endcan

        {{-- Project Management --}}
        @can('project.view')
            <div class="text-xs text-gray-400 px-6 py-3 border-b border-gray-500">
                <p class="text-xs uppercase font-semibold text-gray-400 tracking-wider">Project Management</p>
            </div>
            <a href="{{ route('projects.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm hover:bg-gray-700 {{ request()->routeIs('projects.index') ? 'bg-gray-700' : '' }}">📁
                Projects</a>
            {{-- TAMBAHKAN MENU INI --}}
            <a href="{{ route('tasks.my-tasks') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm hover:bg-gray-700 {{ request()->routeIs('tasks.my-tasks') ? 'bg-gray-700' : '' }}">📝
                Task Saya</a>
            <a href="{{ route('projects.archived') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm hover:bg-gray-700 {{ request()->routeIs('projects.archived') ? 'bg-gray-700' : '' }}">🗃️
                Arsip Project</a>
        @endcan

        @can('resource.view')
            <a href="{{ route('resources.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm hover:bg-gray-700 {{ request()->routeIs('resources.*') ? 'bg-gray-700' : '' }}">🔧
                Resource Master</a>
        @endcan
        @can('report.view')
            <a href="{{ route('reports.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm hover:bg-gray-700 {{ request()->routeIs('reports.*') ? 'bg-gray-700' : '' }}">📊
                Reports</a>
        @endcan

        {{-- Users Management --}}
        @can('user.manage')
            <div class="text-xs text-gray-400 px-6 py-3 border-b border-gray-500">
                <p class="text-xs uppercase font-semibold text-gray-400 tracking-wider">Users Management</p>
            </div>

            <a href="{{ route('users.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm hover:bg-gray-700 {{ request()->routeIs('users.*') ? 'bg-gray-700' : '' }}">👥
                Users</a>
        @endcan

        {{-- Logs Management --}}
        @can('audit.view')
            <div class="text-xs text-gray-400 px-6 py-3 border-b border-gray-500">
                <p class="text-xs uppercase font-semibold text-gray-400 tracking-wider">Logs Management</p>
            </div>

            <a href="{{ route('audit-log.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm hover:bg-gray-700 {{ request()->routeIs('audit-log.*') ? 'bg-gray-700' : '' }}">📋
                Audit Log</a>
        @endcan

        @can('login-history.view')
            <a href="{{ route('login-history.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm hover:bg-gray-700 {{ request()->routeIs('login-history.*') ? 'bg-gray-700' : '' }}">🔐
                Login History</a>
        @endcan

        @can('setting.manage')
            <a href="{{ route('settings.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm hover:bg-gray-700 {{ request()->routeIs('settings.*') ? 'bg-gray-700' : '' }}">⚙️
                Pengaturan</a>
        @endcan

        @if (auth()->user()->hasRole('Programmer'))
            <a href="{{ route('tools.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm hover:bg-gray-700 {{ request()->routeIs('tools.*') ? 'bg-gray-700' : '' }}">🔧
                Tools</a>
        @endif
    </nav>

    {{-- Logout --}}
    <div class="px-4 py-4 border-t border-gray-700">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full text-left flex items-center gap-3 px-3 py-2 rounded-lg text-sm hover:bg-gray-700 text-red-400">🚪
                Logout</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const nav = document.getElementById('sidebar-nav');
            const KEY = 'sidebar_scroll_pos';

            // Restore secepat mungkin
            const saved = sessionStorage.getItem(KEY);

            if (saved !== null) {
                nav.scrollTop = parseInt(saved, 10);
            }

            // Tampilkan setelah restore selesai
            requestAnimationFrame(() => {
                nav.style.opacity = '1';
            });

            // Simpan posisi scroll
            nav.addEventListener('scroll', function() {
                sessionStorage.setItem(KEY, nav.scrollTop);
            }, {
                passive: true
            });

            // Simpan saat klik link
            nav.querySelectorAll('a[href]').forEach(function(link) {
                link.addEventListener('click', function() {
                    sessionStorage.setItem(KEY, nav.scrollTop);
                });
            });
        });
    </script>

</aside>
