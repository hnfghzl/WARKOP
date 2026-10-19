@extends('layout')

@section('title', 'Kelola Kasir - Warkop POS')

@section('content')
<div class="space-y-4 sm:space-y-6">
    <!-- Header: Title, Subtitle, & Add Button -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="flex items-center gap-1.5 mb-1">
                <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 text-[10px] sm:text-xs font-bold tracking-wide uppercase">
                    Manajemen Akun
                </span>
                <span class="text-[10px] sm:text-xs text-slate-400 font-medium">Hak Akses POS</span>
            </div>
            <h1 class="text-lg sm:text-2xl font-bold tracking-tight text-slate-900">
                Kelola Akun Kasir
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-0.5">
                Daftar karyawan yang memiliki akses ke sistem kasir (Point of Sale).
            </p>
        </div>

        <button type="button" 
                onclick="openCreateForm()" 
                id="btnOpenCreate"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 px-4 py-2 sm:py-2.5 text-xs sm:text-sm font-bold text-white shadow-sm transition active:scale-95 self-start sm:self-auto cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Tambah Kasir Baru</span>
        </button>
    </div>

    <!-- Quick Stats (Compact 2x1 on Mobile, 2 cols on Desktop) -->
    <div class="grid grid-cols-2 gap-2.5 sm:gap-5">
        <div class="rounded-xl sm:rounded-2xl bg-white p-3 sm:p-5 border border-slate-100 shadow-sm flex items-center gap-3">
            <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
            <div>
                <p class="text-[10px] sm:text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Kasir</p>
                <p class="text-base sm:text-2xl font-extrabold text-slate-900 mt-0.5">{{ $totalCashiers }} <span class="text-xs font-normal text-slate-400">orang</span></p>
            </div>
        </div>

        <div class="rounded-xl sm:rounded-2xl bg-white p-3 sm:p-5 border border-slate-100 shadow-sm flex items-center gap-3">
            <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
            </div>
            <div>
                <p class="text-[10px] sm:text-xs font-semibold text-slate-400 uppercase tracking-wider">Administrator</p>
                <p class="text-base sm:text-2xl font-extrabold text-slate-900 mt-0.5">{{ $admins->count() }} <span class="text-xs font-normal text-slate-400">admin</span></p>
            </div>
        </div>
    </div>

    <!-- Collapsible Cashier Form (For Create & Edit) -->
    <div id="cashierFormContainer" class="hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-6 border border-slate-200/80 shadow-md">
        <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
            <div>
                <h2 id="formTitle" class="text-sm sm:text-base font-bold text-slate-900 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                    <span>Tambah Akun Kasir</span>
                </h2>
                <p class="text-[11px] sm:text-xs text-slate-400 mt-0.5">Kasir dapat langsung login menggunakan email & password ini.</p>
            </div>
            <button type="button" onclick="closeForm()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition" aria-label="Tutup form">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="cashierForm" method="POST" action="{{ route('cashiers.store') }}">
            @csrf
            <div id="methodSpoofContainer"></div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
                <!-- Nama Kasir -->
                <div>
                    <label for="cashier_name" class="block text-xs font-bold text-slate-700 mb-1">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="cashier_name" 
                           name="name" 
                           required 
                           placeholder="Contoh: Siti Rahma" 
                           class="w-full px-3 py-2 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                </div>

                <!-- Email Kasir -->
                <div>
                    <label for="cashier_email" class="block text-xs font-bold text-slate-700 mb-1">
                        Alamat Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" 
                           id="cashier_email" 
                           name="email" 
                           required 
                           placeholder="siti@warkop.com" 
                           class="w-full px-3 py-2 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                </div>

                <!-- Password -->
                <div>
                    <label for="cashier_password" class="block text-xs font-bold text-slate-700 mb-1">
                        Password <span id="passwordRequiredStar" class="text-red-500">*</span>
                    </label>
                    <input type="password" 
                           id="cashier_password" 
                           name="password" 
                           placeholder="Minimal 6 karakter" 
                           class="w-full px-3 py-2 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                    <p id="passwordHint" class="hidden text-[10px] text-slate-400 mt-1">Kosongkan jika tidak ingin mengubah password.</p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 mt-4 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeForm()" class="px-3.5 py-2 text-xs sm:text-sm font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" id="btnSubmitCashier" class="px-5 py-2 text-xs sm:text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-xs transition active:scale-95 cursor-pointer">
                    Simpan Kasir
                </button>
            </div>
        </form>
    </div>

    <!-- Cashiers Table List (Clean & Compact) -->
    <div class="rounded-xl sm:rounded-2xl bg-white border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-3.5 sm:p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-xs sm:text-sm font-bold text-slate-900 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Daftar Akun Kasir Aktif</span>
                </h2>
                <p class="text-[10px] sm:text-xs text-slate-400 mt-0.5">Akun kasir hanya memiliki akses ke modul penjualan (POS)</p>
            </div>
            <span class="text-[10px] sm:text-xs font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full">
                {{ $totalCashiers }} Akun
            </span>
        </div>

        @if($cashiers->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs min-w-[540px]">
                    <thead class="bg-slate-50/75 border-b border-slate-100 text-slate-400 font-bold">
                        <tr>
                            <th class="py-2.5 px-4 font-semibold">Kasir</th>
                            <th class="py-2.5 px-4 font-semibold">Email Login</th>
                            <th class="py-2.5 px-4 font-semibold text-center">Hak Akses</th>
                            <th class="py-2.5 px-4 font-semibold">Terdaftar Sejak</th>
                            <th class="py-2.5 px-4 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @foreach($cashiers as $cashier)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center text-xs shrink-0">
                                            {{ strtoupper(substr($cashier->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-900">{{ $cashier->name }}</p>
                                            <p class="text-[10px] text-slate-400">ID: #{{ $cashier->id }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-slate-600 font-mono text-[11px]">
                                    {{ $cashier->email }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-bold text-[10px] border border-emerald-200/60">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Kasir POS
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-slate-500 text-[11px]">
                                    {{ $cashier->created_at ? $cashier->created_at->translatedFormat('d M Y') : '-' }}
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- Edit Button -->
                                        <button type="button" 
                                                onclick="openEditForm({{ $cashier->id }}, '{{ addslashes($cashier->name) }}', '{{ addslashes($cashier->email) }}')"
                                                class="px-2.5 py-1 text-[11px] font-bold text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 rounded-lg transition cursor-pointer">
                                            Edit
                                        </button>

                                        <!-- Delete Button -->
                                        <form method="POST" action="{{ route('cashiers.destroy', $cashier) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun kasir {{ $cashier->name }}?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="px-2.5 py-1 text-[11px] font-bold text-rose-600 hover:text-rose-800 bg-rose-50 hover:bg-rose-100 rounded-lg transition cursor-pointer">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="py-8 text-center text-xs text-slate-400">
                <p class="font-bold text-slate-700">Belum ada akun kasir terdaftar</p>
                <p class="text-[11px] mt-0.5">Klik tombol "Tambah Kasir Baru" di atas untuk menambahkan karyawan kasir pertama Anda.</p>
            </div>
        @endif
    </div>

    <!-- Admin Accounts Info Section -->
    <div class="rounded-xl sm:rounded-2xl bg-slate-50 border border-slate-200/70 p-3.5 sm:p-5">
        <h3 class="text-xs sm:text-sm font-bold text-slate-800 flex items-center gap-1.5 mb-2">
            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            <span>Akun Administrator (Pemilik Toko)</span>
        </h3>
        <p class="text-[11px] text-slate-500 mb-3">Administrator memiliki akses penuh ke Dashboard, Laporan Finansial, Inventaris Produk, dan Pengaturan Kasir.</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            @foreach($admins as $admin)
                <div class="flex items-center justify-between p-2.5 rounded-xl bg-white border border-slate-200/70 text-xs">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-[11px]">
                            {{ strtoupper(substr($admin->name, 0, 1)) }}
                        </div>
                        <div>
                            <span class="font-bold text-slate-900 block">{{ $admin->name }}</span>
                            <span class="text-[10px] text-slate-400 font-mono">{{ $admin->email }}</span>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 font-bold text-[10px]">
                        Admin
                    </span>
                </div>
            @endforeach
        </div>
    </div>
</div>

<script>
    function openCreateForm() {
        const container = document.getElementById('cashierFormContainer');
        const form = document.getElementById('cashierForm');
        const title = document.getElementById('formTitle');
        const spoofContainer = document.getElementById('methodSpoofContainer');
        const submitBtn = document.getElementById('btnSubmitCashier');
        const passwordInput = document.getElementById('cashier_password');
        const star = document.getElementById('passwordRequiredStar');
        const hint = document.getElementById('passwordHint');

        form.reset();
        form.action = "{{ route('cashiers.store') }}";
        spoofContainer.innerHTML = '';
        title.innerHTML = '<span class="w-2 h-2 rounded-full bg-blue-600"></span><span>Tambah Akun Kasir Baru</span>';
        submitBtn.textContent = 'Simpan Kasir';
        passwordInput.required = true;
        star.classList.remove('hidden');
        hint.classList.add('hidden');

        container.classList.remove('hidden');
        container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        document.getElementById('cashier_name').focus();
    }

    function openEditForm(id, name, email) {
        const container = document.getElementById('cashierFormContainer');
        const form = document.getElementById('cashierForm');
        const title = document.getElementById('formTitle');
        const spoofContainer = document.getElementById('methodSpoofContainer');
        const submitBtn = document.getElementById('btnSubmitCashier');
        const passwordInput = document.getElementById('cashier_password');
        const star = document.getElementById('passwordRequiredStar');
        const hint = document.getElementById('passwordHint');

        form.reset();
        form.action = "/cashiers/" + id;
        spoofContainer.innerHTML = '@method("PUT")';
        title.innerHTML = '<span class="w-2 h-2 rounded-full bg-amber-500"></span><span>Edit Akun Kasir: ' + name + '</span>';
        submitBtn.textContent = 'Perbarui Data';
        passwordInput.required = false;
        star.classList.add('hidden');
        hint.classList.remove('hidden');

        document.getElementById('cashier_name').value = name;
        document.getElementById('cashier_email').value = email;

        container.classList.remove('hidden');
        container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        document.getElementById('cashier_name').focus();
    }

    function closeForm() {
        document.getElementById('cashierFormContainer').classList.add('hidden');
    }
</script>
@endsection
