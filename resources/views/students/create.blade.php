@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold text-primary">Tambah Siswa Baru</h5>
        </div>
        <div class="card-body p-4">
            
            {{-- PENTING: Tambahkan enctype="multipart/form-data" --}}
            <form action="{{ route('students.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row g-3">
                    {{-- Nama Siswa --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nama Lengkap Siswa</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Budi Santoso" required>
                    </div>

                    {{-- Upload Foto (Fitur Baru) --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Foto Siswa (Opsional)</label>
                        <input type="file" name="photo" class="form-control" accept="image/*">
                        <div class="form-text small">Format: JPG, PNG. Maks: 2MB.</div>
                    </div>

                    {{-- Orang Tua --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Orang Tua / Wali</label>
                        <select name="parent_id" class="form-select" required>
                            <option value="">-- Pilih Orang Tua --</option>
                            @foreach($parents as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->name }} ({{ $parent->phone }})</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Komplek --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Komplek Perumahan</label>
                        <select name="complex_id" class="form-select" required>
                            <option value="">-- Pilih Komplek --</option>
                            @foreach($complexes as $complex)
                                <option value="{{ $complex->id }}">{{ $complex->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Detail Alamat --}}
                    <div class="col-12">
                        <label class="form-label fw-bold">Detail Alamat (Blok/Nomor Rumah)</label>
                        <textarea name="address_note" class="form-control" rows="2" placeholder="Contoh: Blok A5 No. 12, Pagar Hitam"></textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('students.index') }}" class="btn btn-light border">Batal</a>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">Simpan Data</button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection