@extends('layouts.app')

@section('title', 'Sistem Pengajuan Cuti - PT. Company')

@section('content')

{{-- Navbar --}}
<nav class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <span class="text-lg font-bold text-gray-900">CutiSystem</span>
                    <span class="text-xs text-gray-400 block -mt-1">PT. Company</span>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <a href="#fitur" class="text-sm text-gray-600 hover:text-indigo-600 transition">Fitur</a>
                <a href="#cara-kerja" class="text-sm text-gray-600 hover:text-indigo-600 transition">Cara Kerja</a>
                <a href="#hubungi" class="text-sm text-gray-600 hover:text-indigo-600 transition">Hubungi</a>
                <a href="/admin/login" class="bg-indigo-600 text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-indigo-700 transition">
                    Login
                </a>
            </div>
        </div>
    </div>
</nav>

{{-- Hero Section --}}
<section class="hero-gradient relative overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <svg class="w-full h-full" viewBox="0 0 1440 600"><circle cx="200" cy="100" r="300" fill="white"/><circle cx="1200" cy="500" r="200" fill="white"/></svg>
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 lg:py-32 relative">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div class="fade-in">
                <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm text-white/90 text-sm font-medium px-4 py-2 rounded-full mb-6">
                    <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                    Sistem Manajemen Cuti Digital
                </div>
                <h1 class="text-4xl lg:text-5xl font-extrabold text-white leading-tight mb-6">
                    Kelola Pengajuan Cuti<br>
                    <span class="text-indigo-200">Lebih Mudah & Cepat</span>
                </h1>
                <p class="text-lg text-indigo-100 mb-8 leading-relaxed">
                    Sistem terpadu untuk mengelola pengajuan cuti karyawan. Pengajuan otomatis, persetujuan instan, dan pelacakan real-time.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="/admin/login" class="bg-white text-indigo-700 font-semibold px-8 py-3.5 rounded-xl hover:bg-indigo-50 transition shadow-lg">
                        Mulai Sekarang
                    </a>
                    <a href="#cara-kerja" class="border-2 border-white/30 text-white font-semibold px-8 py-3.5 rounded-xl hover:bg-white/10 transition">
                        Pelajari Lebih Lanjut
                    </a>
                </div>
            </div>
            <div class="hidden lg:block fade-in">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                    <div class="bg-white rounded-xl p-5 shadow-xl">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">Pengajuan Disetujui</p>
                                <p class="text-xs text-gray-500">Cuti Tahunan - 3 Hari</p>
                            </div>
                            <span class="ml-auto text-xs bg-green-100 text-green-700 px-3 py-1 rounded-full font-medium">Approved</span>
                        </div>
                        <div class="border-t pt-4 mt-4">
                            <div class="grid grid-cols-3 gap-3 text-center">
                                <div class="bg-indigo-50 rounded-lg p-3">
                                    <p class="text-2xl font-bold text-indigo-600">12</p>
                                    <p class="text-xs text-gray-500">Hari Tersisa</p>
                                </div>
                                <div class="bg-amber-50 rounded-lg p-3">
                                    <p class="text-2xl font-bold text-amber-600">1</p>
                                    <p class="text-xs text-gray-500">Menunggu</p>
                                </div>
                                <div class="bg-green-50 rounded-lg p-3">
                                    <p class="text-2xl font-bold text-green-600">5</p>
                                    <p class="text-xs text-gray-500">Disetujui</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Stats --}}
<section class="bg-white py-12 border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="text-center">
                <p class="text-3xl font-bold text-indigo-600">12+</p>
                <p class="text-sm text-gray-500 mt-1">Karyawan Aktif</p>
            </div>
            <div class="text-center">
                <p class="text-3xl font-bold text-indigo-600">4</p>
                <p class="text-sm text-gray-500 mt-1">Departemen</p>
            </div>
            <div class="text-center">
                <p class="text-3xl font-bold text-indigo-600">3</p>
                <p class="text-sm text-gray-500 mt-1">Jenis Cuti</p>
            </div>
            <div class="text-center">
                <p class="text-3xl font-bold text-indigo-600">24/7</p>
                <p class="text-sm text-gray-500 mt-1">Akses Online</p>
            </div>
        </div>
    </div>
</section>

{{-- Fitur --}}
<section id="fitur" class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Fitur Unggulan</h2>
            <p class="text-gray-500 max-w-2xl mx-auto">Solusi lengkap untuk manajemen pengajuan cuti di perusahaan Anda</p>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            {{-- Card 1 --}}
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 card-hover">
                <div class="w-14 h-14 bg-indigo-100 rounded-2xl flex items-center justify-center mb-6">
                    <svg class="w-7 h-7 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Pengajuan Online</h3>
                <p class="text-gray-500 leading-relaxed">Ajukan cuti kapan saja, di mana saja. Isi form, upload dokumen, dan kirim dalam hitungan menit.</p>
            </div>
            {{-- Card 2 --}}
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 card-hover">
                <div class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center mb-6">
                    <svg class="w-7 h-7 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Approval Workflow</h3>
                <p class="text-gray-500 leading-relaxed">Proses persetujuan oleh Manager dan HR Admin. Status terupdate secara real-time.</p>
            </div>
            {{-- Card 3 --}}
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 card-hover">
                <div class="w-14 h-14 bg-amber-100 rounded-2xl flex items-center justify-center mb-6">
                    <svg class="w-7 h-7 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Notifikasi Email</h3>
                <p class="text-gray-500 leading-relaxed">Pengajuan baru, disetujui, atau ditolak - notifikasi otomatis via email.</p>
            </div>
            {{-- Card 4 --}}
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 card-hover">
                <div class="w-14 h-14 bg-red-100 rounded-2xl flex items-center justify-center mb-6">
                    <svg class="w-7 h-7 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Auto-Cancel</h3>
                <p class="text-gray-500 leading-relaxed">Pengajuan pending lebih dari 7 hari otomatis dibatalkan oleh sistem.</p>
            </div>
            {{-- Card 5 --}}
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 card-hover">
                <div class="w-14 h-14 bg-purple-100 rounded-2xl flex items-center justify-center mb-6">
                    <svg class="w-7 h-7 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Dashboard Statistik</h3>
                <p class="text-gray-500 leading-relaxed">Lihat ringkasan pengajuan, persetujuan, dan saldo cuti dalam satu tampilan.</p>
            </div>
            {{-- Card 6 --}}
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 card-hover">
                <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center mb-6">
                    <svg class="w-7 h-7 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Multi Role (RBAC)</h3>
                <p class="text-gray-500 leading-relaxed">Tiga level akses: Employee, Manager, dan HR Admin dengan hak akses berbeda.</p>
            </div>
        </div>
    </div>
</section>

{{-- Cara Kerja --}}
<section id="cara-kerja" class="bg-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Cara Kerja</h2>
            <p class="text-gray-500">Proses pengajuan cuti hanya dalam 4 langkah</p>
        </div>
        <div class="grid md:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-indigo-600 text-white rounded-2xl flex items-center justify-center text-2xl font-bold mx-auto mb-4">1</div>
                <h4 class="font-bold text-gray-900 mb-2">Login</h4>
                <p class="text-sm text-gray-500">Masuk ke sistem dengan akun yang diberikan</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-indigo-600 text-white rounded-2xl flex items-center justify-center text-2xl font-bold mx-auto mb-4">2</div>
                <h4 class="font-bold text-gray-900 mb-2">Ajukan Cuti</h4>
                <p class="text-sm text-gray-500">Pilih jenis cuti, tanggal, dan upload dokumen</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-indigo-600 text-white rounded-2xl flex items-center justify-center text-2xl font-bold mx-auto mb-4">3</div>
                <h4 class="font-bold text-gray-900 mb-2">Persetujuan</h4>
                <p class="text-sm text-gray-500">Manager meninjau dan menyetujui/menolak</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-indigo-600 text-white rounded-2xl flex items-center justify-center text-2xl font-bold mx-auto mb-4">4</div>
                <h4 class="font-bold text-gray-900 mb-2">Notifikasi</h4>
                <p class="text-sm text-gray-500">Terima email notifikasi hasil keputusan</p>
            </div>
        </div>
    </div>
</section>

{{-- Role --}}
<section class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Tingkat Akses Pengguna</h2>
            <p class="text-gray-500">Setiap role memiliki hak akses yang berbeda</p>
        </div>
        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl p-8 text-white card-hover">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-5">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <h3 class="text-xl font-bold mb-3">Karyawan</h3>
                <ul class="text-indigo-100 text-sm space-y-2">
                    <li>Ajukan cuti baru</li>
                    <li>Lihat riwayat pengajuan</li>
                    <li>Upload dokumen pendukung</li>
                    <li>Batalkan pengajuan pending</li>
                    <li>Lihat saldo cuti</li>
                </ul>
            </div>
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-8 text-white card-hover">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-5">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <h3 class="text-xl font-bold mb-3">Manager</h3>
                <ul class="text-purple-100 text-sm space-y-2">
                    <li>Setujui/tolak pengajuan tim</li>
                    <li>Lihat daftar pengajuan departemen</li>
                    <li>Lihat statistik departemen</li>
                    <li>Dashboard approval</li>
                </ul>
            </div>
            <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl p-8 text-white card-hover">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-5">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h3 class="text-xl font-bold mb-3">HR Admin</h3>
                <ul class="text-emerald-100 text-sm space-y-2">
                    <li>Kelola semua pengajuan</li>
                    <li>Kelola karyawan & departemen</li>
                    <li>Kelola jenis cuti & saldo</li>
                    <li>Lihat laporan keseluruhan</li>
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- Hubungi --}}
<section id="hubungi" class="bg-white py-16 border-t">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-2xl font-bold text-gray-900 mb-3">Siap Memulai?</h2>
        <p class="text-gray-500 mb-8">Hubungi tim HR untuk mendapatkan akun Anda</p>
        <a href="mailto:hr@company.com" class="inline-flex items-center gap-2 bg-indigo-600 text-white font-semibold px-8 py-3.5 rounded-xl hover:bg-indigo-700 transition">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            hr@company.com
        </a>
    </div>
</section>

{{-- Footer --}}
<footer class="bg-gray-900 text-gray-400 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p class="text-sm">&copy; {{ date('Y') }} PT. Company. Sistem Pengajuan Cuti - UAS Cloud Computing</p>
    </div>
</footer>

@endsection
