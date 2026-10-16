@extends('layout')

@section('title', 'Login Admin - Kasir')

@section('content')
<div class="relative w-full max-w-5xl my-4 sm:my-8 px-2">
    <!-- Decorative Dot Grid in Top Right (Matches Reference Image) -->
    <div class="hidden sm:block absolute -top-8 -right-8 w-32 h-32 pointer-events-none opacity-30">
        <svg width="128" height="128" fill="none">
            <pattern id="dotGrid" x="0" y="0" width="16" height="16" patternUnits="userSpaceOnUse">
                <circle cx="3" cy="3" r="2" fill="#FFFFFF"/>
            </pattern>
            <rect width="128" height="128" fill="url(#dotGrid)"/>
        </svg>
    </div>

    <!-- Decorative Dot Grid in Bottom Left -->
    <div class="hidden sm:block absolute -bottom-8 -left-8 w-32 h-32 pointer-events-none opacity-30">
        <svg width="128" height="128" fill="none">
            <rect width="128" height="128" fill="url(#dotGrid)"/>
        </svg>
    </div>

    <!-- Main Split-Screen Card Container -->
    <div class="w-full bg-white rounded-[32px] sm:rounded-[36px] shadow-[0_25px_70px_rgba(20,20,70,0.3)] border border-white/80 overflow-hidden grid grid-cols-1 lg:grid-cols-12 min-h-[580px] relative z-10">
        
        <!-- Left Column: Illustration & Brand (~50% width) -->
        <div class="lg:col-span-6 bg-[#F8FAFF] border-b lg:border-b-0 lg:border-r border-slate-100 p-8 sm:p-10 flex flex-col justify-between relative overflow-hidden">
            <!-- Brand at Top Left (Matches Reference Image Logo) -->
            <div class="flex items-center gap-3 relative z-10">
                <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center shadow-md shadow-indigo-500/30">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <polygon points="12 2 22 8.5 22 15.5 12 22 2 15.5 2 8.5 12 2"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                </div>
                <div>
                    <span class="text-base font-extrabold tracking-widest text-indigo-900 uppercase">WARKOP</span>
                    <span class="text-[10px] text-slate-400 font-bold block -mt-1 tracking-wider uppercase">Sistem Kasir</span>
                </div>
            </div>

            <!-- Center Isometric 3D Illustration -->
            <div class="my-6 flex items-center justify-center relative z-10">
                <div class="w-full max-w-[360px] transition-transform duration-500 hover:scale-105">
                    <img src="{{ asset('images/login-illustration.svg') }}" 
                         alt="Ilustrasi Keamanan Kasir" 
                         class="w-full h-auto drop-shadow-md">
                </div>
            </div>

            <!-- Bottom Note -->
            <div class="flex items-center justify-between text-xs text-slate-400 relative z-10 pt-2 border-t border-slate-200/50">
                <span class="flex items-center gap-1.5 font-medium text-slate-500">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Koneksi Enkripsi Terproteksi
                </span>
                <span class="text-[11px] font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full">v1.0 POS</span>
            </div>
        </div>

        <!-- Right Column: Login Form (~50% width) -->
        <div class="lg:col-span-6 bg-white p-8 sm:p-12 flex flex-col justify-between">
            <!-- Top Right Info (Matches "Already have an account? SIGN IN") -->
            <div class="flex items-center justify-end text-xs font-semibold text-slate-500 gap-2 mb-6">
                <span>Mode Masuk:</span>
                <span class="px-3 py-1 bg-slate-100 text-slate-700 rounded-full font-bold uppercase tracking-wider text-[10px] border border-slate-200">
                    Login Admin
                </span>
            </div>

            <!-- Welcome Title -->
            <div class="mb-7">
                <h1 class="text-3xl sm:text-[34px] font-extrabold text-slate-900 tracking-tight leading-tight">
                    Welcome Rek
                </h1>
                <p class="text-sm text-slate-400 font-medium mt-1.5">
                    Masuk ke akun kasir & admin toko Anda.
                </p>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
                @csrf

                @if ($errors->any())
                    <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-xs text-red-700 flex items-start gap-2.5 shadow-sm">
                        <svg class="w-4 h-4 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <p class="font-bold">Login gagal</p>
                            <p class="mt-0.5">Email atau password yang Anda masukkan salah.</p>
                        </div>
                    </div>
                @endif

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5 tracking-wide">
                        Email
                    </label>
                    <div class="relative">
                        <input id="email"
                               name="email"
                               type="email"
                               value="{{ old('email') }}"
                               autocomplete="email"
                               autofocus
                               required
                               placeholder="focus001@gmail.com"
                               class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm text-slate-800 placeholder:text-slate-300 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/15 @error('email') border-red-400 @enderror">
                    </div>
                    @error('email')
                        <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-bold text-slate-700 tracking-wide">
                            Password
                        </label>
                        <span class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold cursor-pointer" onclick="quickFill('admin@example.com', 'admin123')">
                            Lupa password?
                        </span>
                    </div>
                    <div class="relative">
                        <input id="password"
                               name="password"
                               type="password"
                               autocomplete="current-password"
                               required
                               placeholder="8+ characters"
                               class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 pr-12 text-sm text-slate-800 placeholder:text-slate-300 outline-none transition focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/15">
                        <!-- Toggle Password Button with Eye SVG (Matches Reference Image) -->
                        <button type="button" 
                                onclick="togglePasswordVisibility()" 
                                class="absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 hover:text-slate-600 transition" 
                                aria-label="Tampilkan password">
                            <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember Me Checkbox -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-600">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded text-indigo-600 border-slate-300 focus:ring-indigo-500">
                        <span>Ingat saya</span>
                    </label>
                </div>

                <!-- Submit Button (Matches Purple Pill Button in Reference Image) -->
                <div class="pt-2">
                    <button type="submit" 
                            class="w-full sm:w-auto px-12 py-3.5 bg-gradient-to-r from-[#5B4DF6] to-[#6C5CE7] hover:from-[#4E3FE6] hover:to-[#5E4DE0] text-white font-bold text-sm rounded-2xl shadow-lg shadow-indigo-500/35 hover:shadow-xl hover:shadow-indigo-500/45 transition-all duration-200 active:scale-[0.98]">
                        Login
                    </button>
                </div>
            </form>

            <!-- Bottom Quick Account Fill (Matches Reference Image Social/Quick Row) -->
            <div class="mt-8 pt-6 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-slate-400 font-medium">
                <span>Login cepat akun demo:</span>
                <div class="flex items-center gap-2">
                    <button type="button" 
                            onclick="quickFill('admin@example.com', 'admin123')" 
                            class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 text-slate-700 font-bold transition flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-indigo-600"></span> Admin
                    </button>
                    <button type="button" 
                            onclick="quickFill('test@example.com', 'password')" 
                            class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 text-slate-700 font-bold transition flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Kasir
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePasswordVisibility() {
        const pass = document.getElementById('password');
        const eye = document.getElementById('eyeIcon');
        if (pass.type === 'password') {
            pass.type = 'text';
            eye.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>';
        } else {
            pass.type = 'password';
            eye.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
        }
    }

    function quickFill(email, password) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;
    }
</script>
@endsection