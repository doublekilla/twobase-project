@extends('layouts.admin')

@section('title', 'Tambah Produk')

@section('content')
    <div class="admin-header">
        <div>
            <a href="{{ route('admin.products.index') }}"
                style="color: #6c757d; margin-bottom: 10px; display: inline-block;">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <h1>Tambah Produk Baru</h1>
        </div>
    </div>

    <div style="background: white; border-radius: 16px; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            @if ($errors->any())
                <div
                    style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    <strong><i class="fas fa-exclamation-triangle"></i> Terjadi kesalahan:</strong>
                    <ul style="margin: 10px 0 0 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="form-row">
                <div class="form-group">
                    <label for="name">Nama Produk *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required>
                    @error('name')
                        <span style="color: #e74c3c; font-size: 0.85rem;">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="category_id">Kategori *</label>
                    <select name="category_id" id="category_id" required>
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <span style="color: #e74c3c; font-size: 0.85rem;">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="description">Deskripsi</label>
                <textarea name="description" id="description" rows="4">{{ old('description') }}</textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="price">Harga *</label>
                    <input type="number" name="price" id="price" value="{{ old('price') }}" required min="0">
                    @error('price')
                        <span style="color: #e74c3c; font-size: 0.85rem;">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="discount_price">Harga Diskon</label>
                    <input type="number" name="discount_price" id="discount_price" value="{{ old('discount_price') }}"
                        min="0">
                    <small style="color: #6c757d;">Kosongkan jika tidak ada diskon</small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="stock">Stok *</label>
                    <input type="number" name="stock" id="stock" value="{{ old('stock', 0) }}" required min="0">
                </div>
                <div class="form-group">
                    <label for="size">Ukuran</label>
                    <input type="text" name="size" id="size" value="{{ old('size') }}" placeholder="S,M,L,XL">
                    <small style="color: #6c757d;">Pisahkan dengan koma</small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="color">Warna</label>
                    <input type="text" name="color" id="color" value="{{ old('color') }}" placeholder="Merah,Biru,Hitam">
                    <small style="color: #6c757d;">Pisahkan dengan koma</small>
                </div>
                <div class="form-group">
                    <label for="images">Gambar Produk</label>
                    <input type="file" name="images[]" id="images" multiple accept="image/*" style="padding: 10px;">
                    <small style="color: #6c757d;">Upload satu atau lebih gambar (JPEG, PNG, WebP)</small>
                </div>
            </div>

            <div style="display: flex; gap: 30px; margin: 25px 0;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}
                        style="width: 18px; height: 18px;">
                    <span>Produk Unggulan</span>
                </label>
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                        style="width: 18px; height: 18px;">
                    <span>Aktif</span>
                </label>
            </div>

            <div style="display: flex; gap: 15px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Produk
                </button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>
@endsection