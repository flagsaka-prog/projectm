<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ \App\Models\Setting::get('app_name', config('app.name')) }}</title>

    @if (\App\Models\Setting::get('logo_path'))
        <link rel="icon" type="image/png"
            href="{{ Storage::url(\App\Models\Setting::get('logo_path')) }}?v={{ time() }}">
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Custom Styles untuk mendukung template baru -->
    <style>
        body {
            font-family: 'exo', sans-serif;
        }

        /* Ganti dengan font yang Anda suka */
        .font-exo {
            font-family: 'exo', serif;
        }

        /* Alias untuk judul */
        .font-montserrat {
            font-family: 'exo', sans-serif;
        }

        /* Alias untuk body */

        .bg-secondary {
            background-color: #F3F4F6;
        }

        /* Warna abu-abu terang */
        .sectionSize {
            @apply py-24 px-4 md:px-12 lg:px-48;
        }

        .secondaryTitle {
            @apply text-4xl font-bold mb-12;
        }

        /* Underline Effects */
        .bg-underline1 {
            background-image: linear-gradient(120deg, #6366f1 0%, #6366f1 100%);
            background-repeat: no-repeat;
            background-size: 100% 40%;
            background-position: 0 88%;
        }

        .bg-underline2 {
            background-image: linear-gradient(120deg, #f472b6 0%, #f472b6 100%);
            background-repeat: no-repeat;
            background-size: 100% 40%;
            background-position: 0 88%;
        }

        .bg-underline3 {
            background-image: linear-gradient(120deg, #34d399 0%, #34d399 100%);
            background-repeat: no-repeat;
            background-size: 100% 40%;
            background-position: 0 88%;
        }

        .bg-underline4 {
            background-image: linear-gradient(120deg, #fbbf24 0%, #fbbf24 100%);
            background-repeat: no-repeat;
            background-size: 100% 40%;
            background-position: 0 88%;
        }

        .growing-underline {
            position: relative;
        }

        .growing-underline::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background-color: #000;
            transition: width 0.3s ease;
        }

        .growing-underline:hover::after {
            width: 100%;
        }

        .nav-link-active {
            color: #4f46e5 !important;
            font-weight: bold;
        }
    </style>
</head>

<body class="antialiased bg-white text-gray-800">


    <!-- Navigation -->
    <nav
        class="fixed flex justify-between py-5 w-full lg:px-48 md:px-12 px-4 items-center bg-secondary/80 backdrop-blur-md z-10 border-b border-gray-200">
        <div class="flex items-center gap-2">
            @php $logoPath = \App\Models\Setting::get('logo_path'); @endphp
            @if ($logoPath)
                <img src="{{ Storage::url($logoPath) }}?v={{ time() }}" alt="Logo" class="h-8 w-auto">
            @else
                <span class="text-2xl">📊</span>
            @endif
            <span class="font-bold text-xl text-black tracking-tight">
                {{ \App\Models\Setting::get('app_name', config('app.name')) }}
            </span>
        </div>

        <ul class="font-montserrat items-center hidden md:flex text-black">
            <li class="mx-4"><a class="growing-underline nav-link" href="#howitworks">Cara Kerja</a></li>
            <li class="mx-4"><a class="growing-underline nav-link" href="#features">Fitur</a></li>
            <li class="mx-4"><a class="growing-underline nav-link" href="#faq">FAQ</a></li>
        </ul>

        <div class="font-montserrat hidden md:flex items-center">
            @auth
                <a href="{{ url('/dashboard') }}"
                    class="py-2 px-5 text-white bg-black rounded-3xl hover:bg-gray-800 transition font-semibold flex items-center gap-2">
                    Dashboard
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6">
                        </path>
                    </svg>
                </a>
            @else
                @if (Route::has('login'))
                    <a href="{{ route('login') }}"
                        class="mr-6 text-black hover:text-indigo-600 transition font-semibold">Login</a>
                    <a href="#"
                        class="py-2 px-5 text-white bg-black rounded-3xl hover:bg-gray-800 transition font-semibold">Register</a>
                @endif
            @endauth
        </div>

        <div id="showMenu" class="md:hidden cursor-pointer">
            <svg class="w-8 h-8 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
            </svg>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div id='mobileNav' class="hidden px-6 py-6 fixed top-0 left-0 h-full w-full bg-white z-20 animate-fade-in-down">
        <div id="hideMenu" class="flex justify-end cursor-pointer">
            <svg class="w-8 h-8 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </div>
        <ul class="font-montserrat flex flex-col my-12 items-center text-2xl text-black gap-8">
            <li><a href="#howitworks" class="mobile-link">Cara Kerja</a></li>
            <li><a href="#features" class="mobile-link">Fitur</a></li>
            <li><a href="#faq" class="mobile-link">FAQ</a></li>
            <li class="mt-6">
                @auth
                    <a href="{{ url('/dashboard') }}" class="py-2 px-6 text-white bg-black rounded-3xl">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="py-2 px-6 text-black border-2 border-black rounded-3xl">Login</a>
                @endauth
            </li>
        </ul>
    </div>

    <!-- Hero Section -->
    <section id="home"
        class="pt-28 md:h-screen flex flex-col justify-center text-center md:text-left md:flex-row md:justify-between md:items-center lg:px-48 md:px-12 px-4 bg-secondary">
        <div class="md:flex-1 md:mr-10">
            <h1 class="font-pt-serif text-5xl md:text-6xl font-bold mb-7 text-black leading-tight">
                Kelola Proyek Anda dengan Sistem yang
                <span class="bg-underline1 bg-left-bottom bg-no-repeat pb-2 bg-100%">
                    Lebih Cerdas
                </span>
            </h1>
            <p class="font-pt-serif font-normal mb-7 text-gray-600 text-lg leading-relaxed">
                Kelola proyek dan tim Anda dengan platform yang efisien, terorganisir, dan presisi.
                Tingkatkan produktivitas sekarang.
            </p>
            <div class="font-montserrat flex flex-wrap gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}"
                        class="bg-black px-6 py-4 rounded-lg border-2 border-black border-solid text-white hover:bg-gray-800 transition font-semibold inline-flex items-center gap-2">
                        Buka Dashboard
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="bg-black px-6 py-4 rounded-lg border-2 border-black border-solid text-white hover:bg-gray-800 transition font-semibold">
                        Mulai Sekarang
                    </a>
                @endauth
                <a href="#howitworks"
                    class="px-6 py-4 border-2 border-black border-solid rounded-lg text-black hover:bg-black hover:text-white transition font-semibold">
                    Pelajari Lebih Lanjut
                </a>
            </div>
        </div>
        <div class="flex justify-center md:block mt-12 md:mt-0 md:flex-1">
            <div class="relative w-full max-w-lg">
                <div
                    class="absolute -top-4 -left-4 w-24 h-24 bg-indigo-200 rounded-full filter blur-2xl mix-blend-multiply">
                </div>
                <div
                    class="absolute -bottom-4 -right-4 w-24 h-24 bg-pink-200 rounded-full filter blur-2xl mix-blend-multiply">
                </div>
                <!-- Gunakan background lama Anda di sini jika mau -->
                <img src="{{ asset('image/background.jpg') }}" alt="Ilustrasi Dashboard"
                    class="relative rounded-2xl shadow-2xl border border-gray-200 object-cover w-full h-auto max-h-[400px]" />
            </div>
        </div>
    </section>

    <!-- How it Works -->
    <section id="howitworks" class="bg-black text-white py-24">
        <div class="max-w-5xl mx-auto px-6">
            <div>
                <h2 class="secondaryTitle text-white">Cara <span class="bg-underline2 bg-100%">Kerja</span></h2>
            </div>
            <div class="flex flex-col md:flex-row gap-8">
                <div
                    class="flex-1 flex flex-col items-center my-4 text-center p-6 bg-white/5 rounded-2xl backdrop-blur-sm border border-white/10">
                    <div
                        class="border-2 rounded-full bg-secondary text-black h-12 w-12 flex justify-center items-center mb-4 font-bold text-lg">
                        1</div>
                    <h3 class="font-montserrat font-medium text-xl mb-2">Buat Proyek</h3>
                    <p class="text-gray-300">Inisiasi proyek baru dan atur detail project dengan cepat dan mudah.
                    </p>
                </div>
                <div
                    class="flex-1 flex flex-col items-center my-4 text-center p-6 bg-white/5 rounded-2xl backdrop-blur-sm border border-white/10">
                    <div
                        class="border-2 rounded-full bg-secondary text-black h-12 w-12 flex justify-center items-center mb-4 font-bold text-lg">
                        2</div>
                    <h3 class="font-montserrat font-medium text-xl mb-2">Assign Tim</h3>
                    <p class="text-gray-300">Bagikan tugas kepada tim sesuai peran dan kapabilitas masing-masing.</p>
                </div>
                <div
                    class="flex-1 flex flex-col items-center my-4 text-center p-6 bg-white/5 rounded-2xl backdrop-blur-sm border border-white/10">
                    <div
                        class="border-2 rounded-full bg-secondary text-black h-12 w-12 flex justify-center items-center mb-4 font-bold text-lg">
                        3</div>
                    <h3 class="font-montserrat font-medium text-xl mb-2">Pantau Progres</h3>
                    <p class="text-gray-300">Lacak perkembangan proyek secara real-time dan evaluasi hasilnya.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section id="features" class="py-24 bg-secondary">
        <div class="max-w-5xl mx-auto px-6">
            <div>
                <h2 class="secondaryTitle"><span class="bg-underline3 bg-100%">Fitur</span> Unggulan</h2>
            </div>
            <div class="md:grid md:grid-cols-2 md:grid-rows-2 gap-8">
                <div
                    class="flex items-start font-montserrat my-6 p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="bg-indigo-100 p-3 rounded-lg mr-4 text-indigo-600 text-2xl">📊</div>
                    <div>
                        <h3 class="font-semibold text-xl mb-2 text-black">Gantt Chart & Timeline</h3>
                        <p class="text-gray-600">Visualisasi jadwal proyek secara interaktif untuk memastikan semua
                            milestone tercapai tepat waktu.</p>
                    </div>
                </div>
                <div
                    class="flex items-start font-montserrat my-6 p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="bg-pink-100 p-3 rounded-lg mr-4 text-pink-600 text-2xl">👥</div>
                    <div>
                        <h3 class="font-semibold text-xl mb-2 text-black">Manajemen Sumber Daya</h3>
                        <p class="text-gray-600">Alokasikan anggota tim secara optimal berdasarkan kapasitas dan beban
                            kerja mereka.</p>
                    </div>
                </div>
                <div
                    class="flex items-start font-montserrat my-6 p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="bg-green-100 p-3 rounded-lg mr-4 text-green-600 text-2xl">🔔</div>
                    <div>
                        <h3 class="font-semibold text-xl mb-2 text-black">Notifikasi Real-time</h3>
                        <p class="text-gray-600">Dapatkan peringatan otomatis terkait deadline, perubahan status, dan
                            tugas baru.</p>
                    </div>
                </div>
                <div
                    class="flex items-start font-montserrat my-6 p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="bg-yellow-100 p-3 rounded-lg mr-4 text-yellow-600 text-2xl">📋</div>
                    <div>
                        <h3 class="font-semibold text-xl mb-2 text-black">Laporan project</h3>
                        <p class="text-gray-600">Ekspor laporan proyek yang detail dan terstruktur untuk kebutuhan
                            evaluasi.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section id="faq" class="py-24 bg-white">
        <!-- FAQ dibuat lebih sempit lagi (max-w-4xl) karena teks panjang akan susah dibaca jika terlalu lebar -->
        <div class="max-w-4xl mx-auto px-6">
            <div>
                <h2 class="secondaryTitle"><span class="bg-underline4 bg-100%">FAQ</span></h2>
            </div>

            <div toggleElement class="w-full py-5 border-b border-gray-200 cursor-pointer">
                <div class="flex justify-between items-center">
                    <div question class="font-montserrat font-medium text-lg mr-auto text-black">Apa itu Project
                        Management System?
                    </div>
                    <svg class="w-5 h-5 text-gray-500 transform transition-transform toggle-icon" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                        </path>
                    </svg>
                </div>
                <div answer class="font-montserrat text-gray-600 pb-6 hidden mt-3">
                    Project Management System adalah platform manajemen project yang dirancang untuk membantu tim Anda
                    mengelola
                    proyek agar sesuai secara presisi dan terorganisir.
                </div>
            </div>

            <div toggleElement class="w-full py-5 border-b border-gray-200 cursor-pointer">
                <div class="flex justify-between items-center">
                    <div question class="font-montserrat font-medium text-lg mr-auto text-black">Apakah data proyek
                        saya aman?</div>
                    <svg class="w-5 h-5 text-gray-500 transform transition-transform toggle-icon" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                        </path>
                    </svg>
                </div>
                <div answer class="font-montserrat text-gray-600 pb-6 hidden mt-3">
                    Tentu saja. Keamanan data adalah prioritas kami. Semua informasi dienkripsi dan hanya dapat diakses
                    oleh anggota tim yang berwenang.
                </div>
            </div>

            <div toggleElement class="w-full py-5 border-b border-gray-200 cursor-pointer">
                <div class="flex justify-between items-center">
                    <div question class="font-montserrat font-medium text-lg mr-auto text-black">Bagaimana cara
                        mendapatkan akses?</div>
                    <svg class="w-5 h-5 text-gray-500 transform transition-transform toggle-icon" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                        </path>
                    </svg>
                </div>
                <div answer class="font-montserrat text-gray-600 pb-6 hidden mt-3">
                    Silakan hubungi Administrator atau Manager proyek Anda untuk mendapatkan undangan akun dan akses ke
                    dalam sistem.
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-black text-white py-16">
        <div class="max-w-5xl mx-auto px-6">
            <div class="mb-8 flex items-center gap-2">
                @php $logoPath = \App\Models\Setting::get('logo_path'); @endphp
                @if ($logoPath)
                    <img src="{{ Storage::url($logoPath) }}?v={{ time() }}" alt="Logo"
                        class="h-8 w-auto">
                @else
                    <span class="text-2xl">📊</span>
                @endif

                <span class="font-bold text-xl">{{ \App\Models\Setting::get('app_name', config('app.name')) }}</span>
            </div>
            <div
                class="border-t border-gray-800 pt-8 mt-8 flex flex-col md:flex-row justify-between items-center text-gray-400 text-sm">
                <div>© {{ date('Y') }} {{ \App\Models\Setting::get('app_name', config('app.name')) }}. All rights
                    reserved.</div>
                <div class="mt-4 md:mt-0 opacity-50 tracking-tighter">VERSION 1.0.0</div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        // Mobile Menu Toggle
        document.getElementById('showMenu').addEventListener('click', () => {
            document.getElementById('mobileNav').classList.remove('hidden');
        });
        document.getElementById('hideMenu').addEventListener('click', () => {
            document.getElementById('mobileNav').classList.add('hidden');
        });
        document.querySelectorAll('.mobile-link').forEach(link => {
            link.addEventListener('click', () => {
                document.getElementById('mobileNav').classList.add('hidden');
            });
        });

        // FAQ Toggle
        document.querySelectorAll('[toggleElement]').forEach(el => {
            el.addEventListener('click', () => {
                const answer = el.querySelector('[answer]');
                const icon = el.querySelector('.toggle-icon');
                answer.classList.toggle('hidden');
                icon.classList.toggle('rotate-180');
            });
        });

        // Scroll Spy Navbar
        window.addEventListener('scroll', () => {
            let current = '';
            const sections = document.querySelectorAll('section[id]');
            const navLinks = document.querySelectorAll('.nav-link');

            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if (pageYOffset >= (sectionTop - sectionHeight / 3)) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('nav-link-active');
                if (link.getAttribute('href').includes(current)) {
                    link.classList.add('nav-link-active');
                }
            });
        });
    </script>
</body>

</html>
