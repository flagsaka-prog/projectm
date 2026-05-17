<x-guest-layout>
    <!-- Logo & Branding (Sama seperti Landing Page) -->
    <div class="text-center mb-8">
        @php $logoPath = \App\Models\Setting::get('logo_path'); @endphp
        <div class="flex items-center justify-center gap-2 mb-6">
            @if ($logoPath)
                <img src="{{ Storage::url($logoPath) }}?v={{ time() }}" alt="Logo" class="h-10 w-auto">
            @else
                <span class="text-3xl">📊</span>
            @endif
            <span class="text-xl font-bold text-black tracking-tight">
                {{ \App\Models\Setting::get('app_name', config('app.name')) }}
            </span>
        </div>
        <h2 class="text-2xl font-bold text-black">Selamat Datang Kembali</h2>
        <p class="text-gray-500 mt-1 text-sm">Silakan masuk ke akun Anda untuk melanjutkan</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status
        class="mb-4 rounded-xl bg-green-50 p-3 text-green-700 border border-green-200 text-sm font-medium"
        :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-5">
            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                Email
            </label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                autocomplete="username"
                class="block w-full rounded-xl border border-gray-200 bg-gray-50 py-3 px-4 text-black placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:bg-white transition duration-200"
                placeholder="nama@perusahaan.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs text-red-500 font-medium" />
        </div>

        <!-- Password -->
        <div class="mb-5">
            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                Password
            </label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                class="block w-full rounded-xl border border-gray-200 bg-gray-50 py-3 px-4 text-black placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:bg-white transition duration-200"
                placeholder="••••••••">
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs text-red-500 font-medium" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between mt-6 mb-6">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox"
                    class="rounded border-gray-300 text-black shadow-sm focus:ring-black" name="remember">
                <span class="ms-2 text-sm text-gray-600">Ingat saya</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-indigo-600 hover:text-indigo-800 font-semibold hover:underline"
                    href="{{ route('password.request') }}">
                    Lupa password?
                </a>
            @endif
        </div>

        <!-- Submit Button (Sesuai tema tombol hitam Landing Page) -->
        <div>
            <button type="submit"
                class="w-full bg-black text-white py-3 px-6 rounded-xl font-semibold shadow-lg hover:bg-gray-800 transition duration-300 active:scale-95 flex items-center justify-center gap-2">
                Masuk
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
            </button>
        </div>
    </form>
</x-guest-layout>
