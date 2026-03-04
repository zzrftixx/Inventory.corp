@extends('layouts.app')

@section('title', 'Tambah Barang')
@section('header')
    <div class="flex items-center">
        <a href="{{ route('items.index') }}" class="text-slate-400 hover:text-slate-600 mr-3 transition-colors">
            <i class="ph ph-arrow-left text-xl"></i>
        </a>
        Tambah Barang Master
    </div>
@endsection

@section('content')
    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mt-6">
        <div class="p-6 border-b border-slate-100">
            <h2 class="text-lg font-semibold text-slate-800">Detail Barang Baru</h2>
            <p class="text-sm text-slate-500">Masukkan spesifikasi barang, kategori, dan limit stok.</p>
        </div>

        <form action="{{ route('items.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="kode_barang" class="block text-sm font-medium text-slate-700 mb-1">Kode Barang <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="kode_barang" id="kode_barang" value="{{ old('kode_barang') }}" required
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all"
                        placeholder="Contoh: ALM-001">
                    @error('kode_barang')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-medium text-slate-700 mb-1">Kategori <span
                            class="text-red-500">*</span></label>
                    <select name="category_id" id="category_id" required
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all bg-white">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-1 md:col-span-2">
                    <label for="nama_barang" class="block text-sm font-medium text-slate-700 mb-1">Nama Barang Lengkap <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="nama_barang" id="nama_barang" value="{{ old('nama_barang') }}" required
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all"
                        placeholder="Contoh: Handle Stainless Steel 304">
                    @error('nama_barang')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4 col-span-1 md:col-span-2">
                    <div>
                        <label for="satuan" class="block text-sm font-medium text-slate-700 mb-1">Satuan <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="satuan" id="satuan" value="{{ old('satuan', 'PCS') }}" required
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all"
                            placeholder="PCS / SET / LBR / BTH">
                        @error('satuan')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="harga_jual_default" class="block text-sm font-medium text-slate-700 mb-1">Harga Jual
                            Default (Rp) <span class="text-red-500">*</span></label>
                        <input type="text" inputmode="numeric" name="harga_jual_default" id="harga_jual_default"
                            value="{{ old('harga_jual_default', 0) }}" required
                            class="input-rupiah w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all"
                            onkeyup="formatRupiah(this)">
                        @error('harga_jual_default')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="col-span-1 md:col-span-2 mt-4 p-4 border border-sky-100 bg-sky-50 rounded-lg">
                    <div class="flex items-center mb-4">
                        <input type="checkbox" name="is_aluminium" id="is_aluminium" value="1" {{ old('is_aluminium') ? 'checked' : '' }}
                            class="w-4 h-4 text-primary bg-white border-slate-300 rounded focus:ring-primary focus:ring-2">
                        <label for="is_aluminium" class="ml-2 text-sm font-semibold text-slate-800">
                            Hitung Sebagai Batang Aluminium (Rumus: Kg x Meter x Harga Dasar)
                        </label>
                    </div>

                    <div id="aluminium_fields"
                        class="grid grid-cols-1 md:grid-cols-3 gap-4 {{ old('is_aluminium') ? '' : 'hidden' }}">
                        <div>
                            <label for="berat_profil_kg" class="block text-xs font-medium text-slate-700 mb-1">Berat Profil
                                (Kg) <span class="text-red-500">*</span></label>
                            <input type="number" step="0.001" name="berat_profil_kg" id="berat_profil_kg"
                                value="{{ old('berat_profil_kg') }}"
                                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-1 focus:ring-primary outline-none text-right">
                        </div>
                        <div>
                            <label for="panjang_meter" class="block text-xs font-medium text-slate-700 mb-1">Panjang (Meter)
                                <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" name="panjang_meter" id="panjang_meter"
                                value="{{ old('panjang_meter', 6) }}"
                                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-1 focus:ring-primary outline-none text-right">
                        </div>
                        <div>
                            <label for="harga_dasar_aluminium_kg"
                                class="block text-xs font-medium text-slate-700 mb-1">Harga Dasar /Kg (Rp) <span
                                    class="text-red-500">*</span></label>
                            <input type="text" inputmode="numeric" name="harga_dasar_aluminium_kg"
                                id="harga_dasar_aluminium_kg" value="{{ old('harga_dasar_aluminium_kg', 0) }}"
                                class="input-rupiah w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-1 focus:ring-primary outline-none text-right"
                                onkeyup="formatRupiah(this)">
                        </div>
                    </div>
                </div>

                <div
                    class="grid grid-cols-2 gap-4 col-span-1 md:col-span-2 bg-slate-50 p-4 rounded-lg border border-slate-200 mt-4">
                    <div>
                        <label for="stok_saat_ini" class="block text-sm font-medium text-slate-700 mb-1">Stok Awal <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="stok_saat_ini" id="stok_saat_ini" value="{{ old('stok_saat_ini', 0) }}"
                            required min="0"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">
                        <p class="text-xs text-slate-500 mt-1">Stok fisik saat pertama kali input.</p>
                        @error('stok_saat_ini')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="batas_stok_minimum" class="block text-sm font-medium text-slate-700 mb-1">Batas Stok
                            Minimum <span class="text-red-500">*</span></label>
                        <input type="number" name="batas_stok_minimum" id="batas_stok_minimum"
                            value="{{ old('batas_stok_minimum', 10) }}" required min="0"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all">
                        <p class="text-xs text-slate-500 mt-1">Sistem akan peringatkan restock (PO) jika stok di bawah ini.
                        </p>
                        @error('batas_stok_minimum')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

            </div>

            <div class="flex justify-end pt-4 border-t border-slate-100 space-x-3">
                <a href="{{ route('items.index') }}"
                    class="px-5 py-2.5 text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">Batal</a>
                <button type="submit"
                    class="px-5 py-2.5 text-sm font-medium text-white bg-primary hover:bg-sky-600 rounded-lg transition-colors shadow-sm">Simpan
                    Barang</button>
            </div>
        </form>
    </div>

    <script>
        function formatRupiah(obj) {
            let val = obj.value.replace(/[^0-9]/g, '');
            if (val !== '') {
                obj.value = new Intl.NumberFormat('id-ID').format(parseInt(val, 10));
            } else {
                obj.value = '';
            }
        }

        document.querySelector('form').addEventListener('submit', function (e) {
            // Bersihkan titik format sebelum submit
            document.querySelectorAll('.input-rupiah').forEach(function (input) {
                input.value = input.value.replace(/\./g, '');
            });
        });

        // Format nilai awal saat load
        document.addEventListener("DOMContentLoaded", function () {
            let initialHarga = document.getElementById('harga_jual_default');
            if (initialHarga && initialHarga.value !== "0" && initialHarga.value !== "") {
                formatRupiah(initialHarga);
            }

            let alumHarga = document.getElementById('harga_dasar_aluminium_kg');
            if (alumHarga && alumHarga.value !== "0" && alumHarga.value !== "") {
                formatRupiah(alumHarga);
            }

            const checkbox = document.getElementById('is_aluminium');
            const fields = document.getElementById('aluminium_fields');
            const inputs = fields.querySelectorAll('input');

            checkbox.addEventListener('change', function () {
                if (this.checked) {
                    fields.classList.remove('hidden');
                    inputs.forEach(el => el.required = true);
                } else {
                    fields.classList.add('hidden');
                    inputs.forEach(el => el.required = false);
                }
            });

            // Trigger initial state against old inputs if validation fails
            if (checkbox.checked) {
                inputs.forEach(el => el.required = true);
            }
        });
    </script>
@endsection