@extends('layouts.admin')

@section('title', 'Edit Kategori')

@section('content')
    <div class="admin-header">
        <div>
            <a href="{{ route('admin.categories.index') }}"
                style="color: #6c757d; margin-bottom: 10px; display: inline-block;">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <h1>Edit Kategori</h1>
        </div>
    </div>

    <div
        style="background: white; border-radius: 16px; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); max-width: 600px;">
        <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Nama Kategori *</label>
                <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required>
                @error('name')
                    <span style="color: #e74c3c; font-size: 0.85rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">Deskripsi</label>
                <textarea name="description" id="description"
                    rows="4">{{ old('description', $category->description) }}</textarea>
            </div>

            <div class="form-group">
                <label for="image">Gambar Kategori</label>
                <input type="file" name="image" id="image" accept="image/*" style="padding: 10px;">
                @if($category->image)
                    <small style="color: #6c757d;">Gambar saat ini tersimpan. Upload baru untuk mengganti.</small>
                @endif
            </div>

            <div style="margin: 25px 0;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }} style="width: 18px; height: 18px;">
                    <span>Aktif</span>
                </label>
            </div>

            <div style="display: flex; gap: 15px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>
@endsection