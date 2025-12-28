@extends('layouts.app')

@section('title', 'Profil Saya - TWOBASE')

@section('content')
    <section style="padding: 40px 0 80px;">
        <div class="container">
            <h1 style="font-size: 2rem; font-weight: 700; color: #1d3557; margin-bottom: 30px;">Profil Saya</h1>

            <div style="max-width: 600px;">
                <div style="background: white; border-radius: 16px; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="name">Nama Lengkap</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <span style="color: #e74c3c; font-size: 0.85rem;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" value="{{ $user->email }}" disabled style="background: #f8f9fa;">
                            <small style="color: #6c757d;">Email tidak dapat diubah</small>
                        </div>

                        <div class="form-group">
                            <label for="phone">Nomor Telepon</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                                placeholder="08xxxxxxxxxx">
                            @error('phone')
                                <span style="color: #e74c3c; font-size: 0.85rem;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="address">Alamat</label>
                            <textarea name="address" id="address" rows="4"
                                placeholder="Alamat lengkap untuk pengiriman">{{ old('address', $user->address) }}</textarea>
                            @error('address')
                                <span style="color: #e74c3c; font-size: 0.85rem;">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection