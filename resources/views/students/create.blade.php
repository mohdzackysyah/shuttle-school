@extends('layouts.admin')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Tambah Siswa Baru</div>
            <div class="card-body">
                <form action="{{ route('students.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap Siswa</label>
                        <input type="text" class="form-control" name="name" placeholder="Contoh: Budi Santoso Junior" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Orang Tua / Wali</label>
                            <select name="parent_id" class="form-select" required>
                                <option value="">-- Pilih Orang Tua --</option>
                                @foreach($parents as $parent)
                                    <option value="{{ $parent->id }}">{{ $parent->name }} ({{ $parent->phone }})</option>
                                @endforeach
                            </select>
                            @if($parents->isEmpty())
                                <div class="form-text text-danger">Data Wali Murid kosong. Input data Wali Murid dulu.</div>
                            @endif
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Komplek Tempat Tinggal</label>
                            <select name="complex_id" class="form-select" required>
                                <option value="">-- Pilih Komplek --</option>
                                @foreach($complexes as $complex)
                                    <option value="{{ $complex->id }}">{{ $complex->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Detail Alamat (Blok / Nomor Rumah)</label>
                        <input type="text" class="form-control" name="address_note" placeholder="Contoh: Blok A No. 12">
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('students.index') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Simpan Data Siswa</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection