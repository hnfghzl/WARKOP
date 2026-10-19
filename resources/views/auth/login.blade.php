@extends('layout')

@section('title', 'Login Admin - Kasir')

@section('content')
<div class="relative w-full max-w-4xl my-auto py-1 sm:py-3 px-1 sm:px-2">
    <!-- Decorative Dot Grid in Top Right -->
    <div class="hidden sm:block absolute -top-6 -right-6 w-24 h-24 pointer-events-none opacity-25">
        <svg width="96" height="96" fill="none">
            <pattern id="dotGrid" x="0" y="0" width="16" height="16" patternUnits="userSpaceOnUse">
                <circle cx="3" cy="3" r="2" fill="#FFFFFF"/>
            </pattern>
            <rect width="96" height="96" fill="url(#dotGrid)"/>
        </svg>
    </div>

    <!-- Decorative Dot Grid in Bottom Left -->
    <div class="hidden sm:block absolute -bottom-6 -left-6 w-24 h-24 pointer-events-none opacity-25">
        <svg width="96" height="96" fill="none">
            <rect width="96" height="96" fill="url(#dotGrid)"/>
        </svg>
    </div>

    <!-- Main Split-Screen Card Container (Compact height) -->
    <div class="w-full bg-white rounded-2xl sm:rounded-[32px] shadow-[0_20px_60px_rgba(20,20,70,0.25)] border border-white/80 overflow-hidden grid grid-cols-1 lg:grid-cols-12 min-h-0 relative z-10">
        
        <!-- Left Column: Illustration & Brand (Desktop only) -->
        <div class="hidden lg:flex lg:col-span-6 bg-[#F8FAFF] border-r border-slate-100 p-6 sm:p-8 flex-col justify-between relative overflow-hidden">
            <!-- Brand at Top Left -->
            <div class="flex items-center gap-2.5 relative z-10">
                <div class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center shadow-md shadow-indigo-500/25">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <polygon points="12 2 22 8.5 22 15.5 12 22 2 15.5 2 8.5 12 2"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                </div>
                <div>
                    <span class="text-sm font-extrabold tracking-widest text-indigo-900 uppercase">WARKOP</span>
                    <span class="text-[9px] text-slate-400 font-bold block -mt-1 tracking-wider uppercase">Sistem Kasir</span>
                </div>
            </div>

            <!-- Center Isometric 3D Illustration -->
            <div class="my-3 flex items-center justify-center relative z-10">
                <div class="w-full max-w-[260px] transition-transform duration-500 hover:scale-105">
                    <img src="{{ asset('images/login-illustration.svg') }}" 
                         alt="Ilustrasi Keamanan Kasir" 
                         class="w-full h-auto drop-shadow-sm">
                </div>
            </div>

            <!-- Bottom Note -->
            <div class="flex items-center justify-between text-[11px] text-slate-400 relative z-10 pt-2 border-t border-slate-200/50">
                <span class="flex items-center gap-1.5 font-medium text-slate-500">
                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Enkripsi Terproteksi
                </span>
                <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full">v1.0 POS</span>
            </div>
        </div>

        <!-- Right Column: Login Form (Compact padding, no excessive vertical length) -->
        <div class="lg:col-span-6 bg-white p-4 sm:p-7 lg:p-8 flex flex-col justify-between">
            <!-- Mobile Brand & Mode Header (Compact single row) -->
            <div class="lg:hidden flex items-center justify-between pb-2.5 mb-2.5 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-indigo-600 text-white flex items-center justify-center shadow-xs">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <polygon points="12 2 22 8.5 22 15.5 12 22 2 15.5 2 8.5 12 2"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs font-extrabold tracking-widest text-indigo-900 uppercase">WARKOP POS</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded-full font-bold uppercase tracking-wider text-[9px] border border-slate-200">
                    Login Admin
                </span>
            </div>

            <!-- Desktop Top Right Info -->
            <div class="hidden lg:flex items-center justify-end text-xs font-semibold text-slate-500 gap-1.5 mb-2">
                <span>Mode:</span>
                <span class="px-2.5 py-0.5 bg-slate-100 text-slate-700 rounded-full font-bold uppercase tracking-wider text-[10px] border border-slate-200">
                    Login Admin
                </span>
            </div>

            <!-- Welcome Title -->
            <div class="mb-3 sm:mb-4">
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight leading-tight">
                    Welcome Rek 👋
                </h1>
                <p class="text-xs sm:text-sm text-slate-400 font-medium mt-0.5">
                    Masuk ke akun kasir & admin toko Anda.
                </p>
            </div>

            <!-- Form (Compact spacing) -->
            <form id="loginForm" method="POST" action="{{ route('login.post') }}" class="space-y-3 sm:space-y-3.5">
                @csrf

                @if ($errors->any())
                    <div class="rounded-xl border border-red-200 bg-red-50 p-2.5 text-xs text-red-700 flex items-start gap-2 shadow-xs">
                        <svg class="w-3.5 h-3.5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <p class="font-bold">Login gagal</p>
                            <p class="text-[11px] mt-0.5">Email atau password yang Anda masukkan salah.</p>
                        </div>
                    </div>
                @endif

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-[11px] sm:text-xs font-bold text-slate-700 mb-1 tracking-wide">
                        Email
                    </label>
                    <input id="email"
                           name="email"
                           type="email"
                           value="{{ old('email') }}"
                           autocomplete="email"
                           autofocus
                           required
                           placeholder="admin@example.com"
                           class="w-full rounded-xl sm:rounded-2xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 placeholder:text-slate-300 outline-none transition focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/15 @error('email') border-red-400 @enderror">
                    @error('email')
                        <p class="mt-0.5 text-[11px] text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-[11px] sm:text-xs font-bold text-slate-700 mb-1 tracking-wide">
                        Password
                    </label>
                    <div class="relative">
                        <input id="password"
                               name="password"
                               type="password"
                               autocomplete="current-password"
                               required
                               placeholder="8+ characters"
                               class="w-full rounded-xl sm:rounded-2xl border border-slate-200 bg-white px-3.5 py-2.5 pr-10 text-xs sm:text-sm text-slate-800 placeholder:text-slate-300 outline-none transition focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/15">
                        <!-- Toggle Password Button with Eye SVG -->
                        <button type="button" 
                                onclick="togglePasswordVisibility()" 
                                class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-slate-600 transition" 
                                aria-label="Tampilkan password">
                            <svg id="eyeIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember Me & Forgot Password in One Compact Row -->
                <div class="flex items-center justify-between pt-0.5">
                    <label class="flex items-center gap-1.5 cursor-pointer text-[11px] sm:text-xs font-semibold text-slate-600">
                        <input type="checkbox" name="remember" class="w-3.5 h-3.5 rounded text-indigo-600 border-slate-300 focus:ring-indigo-500">
                        <span>Ingat saya</span>
                    </label>
                    <span class="text-[11px] sm:text-xs text-indigo-600 hover:text-indigo-800 font-semibold cursor-pointer" onclick="quickFill('admin@example.com', 'password')">
                        Lupa password?
                    </span>
                </div>

                <!-- Submit Button -->
                <div class="pt-1">
                    <button type="submit" id="btnSubmitLogin"
                            class="w-full sm:w-auto px-10 py-2.5 sm:py-3 bg-gradient-to-r from-[#5B4DF6] to-[#6C5CE7] hover:from-[#4E3FE6] hover:to-[#5E4DE0] text-white font-bold text-xs sm:text-sm rounded-xl sm:rounded-2xl shadow-md shadow-indigo-500/30 transition-all duration-200 active:scale-[0.98]">
                        Login
                    </button>
                </div>
            </form>

            <!-- Bottom Quick Account Fill / 1-Click Instant Login (Compact 2-columns) -->
            <div class="mt-4 pt-3 sm:pt-4 border-t border-slate-100">
                <div class="rounded-xl sm:rounded-2xl border border-indigo-100/90 bg-gradient-to-r from-indigo-50/50 via-slate-50/70 to-blue-50/50 p-2.5 sm:p-3 shadow-xs">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] sm:text-xs font-bold text-slate-800 flex items-center gap-1">
                            Login Cepat Akun Demo:
                        </span>
                        <span class="text-[9px] sm:text-[10px] font-bold text-indigo-700 bg-white border border-indigo-100 px-2 py-0.5 rounded-full shadow-xs">
                            1-Klik
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <!-- Admin Demo Card -->
                        <button type="button" 
                                onclick="quickLogin('admin@example.com', 'password', this)" 
                                class="group flex flex-col items-start p-2 sm:p-2.5 rounded-lg bg-white border border-slate-200/90 hover:border-indigo-400 hover:shadow-xs transition-all active:scale-[0.98] text-left">
                            <div class="flex items-center justify-between w-full">
                                <span class="text-[11px] sm:text-xs font-bold text-slate-800 group-hover:text-indigo-600 transition flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-600"></span> Admin
                                </span>
                                <span class="text-[10px] text-indigo-600 font-bold group-hover:translate-x-0.5 transition-transform">
                                    →
                                </span>
                            </div>
                            <span class="mt-0.5 text-[9px] sm:text-[10px] text-slate-400 font-mono truncate w-full">
                                admin@example.com
                            </span>
                        </button>

                        <!-- Kasir Demo Card -->
                        <button type="button" 
                                onclick="quickLogin('test@example.com', 'password', this)" 
                                class="group flex flex-col items-start p-2 sm:p-2.5 rounded-lg bg-white border border-slate-200/90 hover:border-emerald-400 hover:shadow-xs transition-all active:scale-[0.98] text-left">
                            <div class="flex items-center justify-between w-full">
                                <span class="text-[11px] sm:text-xs font-bold text-slate-800 group-hover:text-emerald-600 transition flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Kasir
                                </span>
                                <span class="text-[10px] text-emerald-600 font-bold group-hover:translate-x-0.5 transition-transform">
                                    →
                                </span>
                            </div>
                            <span class="mt-0.5 text-[9px] sm:text-[10px] text-slate-400 font-mono truncate w-full">
                                test@example.com
                            </span>
                        </button>
                    </div>

                    <p class="mt-1.5 text-[10px] text-slate-400 text-center">
                        Klik akun di atas untuk langsung masuk tanpa mengetik.
                    </p>
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
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        if (emailInput && passwordInput) {
            emailInput.value = email;
            passwordInput.value = password;
        }
    }

    function quickLogin(email, password, btnElement) {
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const form = document.getElementById('loginForm');
        const submitBtn = document.getElementById('btnSubmitLogin');

        if (emailInput && passwordInput && form) {
            emailInput.value = email;
            passwordInput.value = password;

            if (btnElement) {
                btnElement.classList.add('opacity-75', 'pointer-events-none');
            }

            if (submitBtn) {
                submitBtn.textContent = 'Memproses...';
                submitBtn.disabled = true;
            }

            setTimeout(() => {
                form.submit();
            }, 100);
        }
    }
</script>
@endsection