@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold text-warning">Edit Data Siswa</h5>
        </div>
        <div class="card-body p-4">
            
            <form action="{{ route('students.update', $student->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row g-3">
                    {{-- Preview Foto Lama --}}
                    <div class="col-12 text-center mb-3">
                        @if($student->photo)
                            <img src="{{ asset('storage/' . $student->photo) }}" alt="Foto Siswa" class="rounded-circle shadow-sm" style="width: 100px; height: 100px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center mx-auto text-secondary fw-bold" style="width: 100px; height: 100px; font-size: 2rem;">
                                {{ substr($student->name, 0, 1) }}
                            </div>
                        @endif
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nama Lengkap Siswa</label>
                        <input type="text" name="name" class="form-control" value="{{ $student->name }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Ganti Foto (Opsional)</label>
                        <input type="file" name="photo" class="form-control" accept="image/*">
                        <div class="form-text small">Biarkan kosong jika tidak ingin mengganti foto.</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Orang Tua / Wali</label>
                        <select name="parent_id" class="form-select" required>
                            @foreach($parents as $parent)
                                <option value="{{ $parent->id }}" {{ $student->parent_id == $parent->id ? 'selected' : '' }}>
                                    {{ $parent->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Komplek Perumahan</label>
                        <select name="complex_id" class="form-select" required>
                            @foreach($complexes as $complex)
                                <option value="{{ $complex->id }}" {{ $student->complex_id == $complex->id ? 'selected' : '' }}>
                                    {{ $complex->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold">Detail Alamat</label>
                        <textarea name="address_note" class="form-control" rows="2">{{ $student->address_note }}</textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('students.index') }}" class="btn btn-light border">Batal</a>
                    <button type="submit" class="btn btn-warning px-4 fw-bold">Update Data</button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection