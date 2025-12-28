@extends('layouts.admin')

@section('title', 'Edit Produk')

@section('content')
    <div class="admin-header">
        <div>
            <a href="{{ route('admin.products.index') }}"
                style="color: #6c757d; margin-bottom: 10px; display: inline-block;">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <h1>Edit Produk</h1>
        </div>
    </div>

    <div style="background: white; border-radius: 16px; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

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
                    <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required>
                    @error('name')
                        <span style="color: #e74c3c; font-size: 0.85rem;">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="category_id">Kategori *</label>
                    <select name="category_id" id="category_id" required>
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="description">Deskripsi</label>
                <textarea name="description" id="description"
                    rows="4">{{ old('description', $product->description) }}</textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="price">Harga *</label>
                    <input type="number" name="price" id="price" value="{{ old('price', $product->price) }}" required
                        min="0">
                </div>
                <div class="form-group">
                    <label for="discount_price">Harga Diskon</label>
                    <input type="number" name="discount_price" id="discount_price"
                        value="{{ old('discount_price', $product->discount_price) }}" min="0">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="stock">Stok *</label>
                    <input type="number" name="stock" id="stock" value="{{ old('stock', $product->stock) }}" required
                        min="0">
                </div>
                <div class="form-group">
                    <label for="size">Ukuran</label>
                    <input type="text" name="size" id="size" value="{{ old('size', $product->size) }}"
                        placeholder="S,M,L,XL">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="color">Warna</label>
                    <input type="text" name="color" id="color" value="{{ old('color', $product->color) }}"
                        placeholder="Merah,Biru,Hitam">
                </div>
                <div class="form-group">
                    <label for="images">Tambah Gambar</label>
                    <input type="file" name="images[]" id="images" multiple accept="image/*" style="padding: 10px;">
                </div>
            </div>

            @if($product->images->count() > 0)
                <div class="form-group">
                    <label>Gambar Saat Ini (klik "Set Utama" untuk memilih gambar yang ditampilkan)</label>
                    <div style="display: flex; gap: 15px; flex-wrap: wrap; margin-top: 10px;">
                        @foreach($product->images as $image)
                            <div style="position: relative; width: 120px;">
                                <div
                                    style="width: 120px; height: 120px; background: #f8f9fa; border-radius: 8px; overflow: hidden; {{ $image->is_primary ? 'border: 3px solid #e63946;' : 'border: 2px solid #dee2e6;' }}">
                                    <img src="{{ asset('storage/' . $image->image_path) }}" alt="Gambar Produk"
                                        style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                                <button type="button" onclick="deleteImage({{ $image->id }})"
                                    style="position: absolute; top: -8px; right: -8px; width: 24px; height: 24px; background: #e74c3c; color: white; border: none; border-radius: 50%; cursor: pointer; font-size: 0.7rem;">
                                    <i class="fas fa-times"></i>
                                </button>
                                @if($image->is_primary)
                                    <div style="text-align: center; margin-top: 5px;">
                                        <span
                                            style="background: #e63946; color: white; padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 600;">
                                            <i class="fas fa-star"></i> UTAMA
                                        </span>
                                    </div>
                                @else
                                    <div style="text-align: center; margin-top: 5px;">
                                        <button type="button" onclick="setPrimaryImage({{ $image->id }})"
                                            style="background: #2ecc71; color: white; border: none; padding: 4px 10px; border-radius: 4px; font-size: 0.7rem; cursor: pointer; font-weight: 600;">
                                            <i class="fas fa-check"></i> Set Utama
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div style="display: flex; gap: 30px; margin: 25px 0;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }} style="width: 18px; height: 18px;">
                    <span>Produk Unggulan</span>
                </label>
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }} style="width: 18px; height: 18px;">
                    <span>Aktif</span>
                </label>
            </div>

            <div style="display: flex; gap: 15px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>

    <!-- Hidden forms for image actions (outside the main form) -->
    <form id="deleteImageForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
    <form id="setPrimaryImageForm" method="POST" style="display: none;">
        @csrf
        @method('PUT')
    </form>

    @push('scripts')
        <script>
            function deleteImage(imageId) {
                if (confirm('Yakin ingin menghapus gambar ini?')) {
                    const form = document.getElementById('deleteImageForm');
                    form.action = '/admin/products/images/' + imageId;
                    form.submit();
                }
            }

            function setPrimaryImage(imageId) {
                const form = document.getElementById('setPrimaryImageForm');
                form.action = '/admin/products/images/' + imageId + '/primary';
                form.submit();
            }
        </script>
    @endpush
@endsection