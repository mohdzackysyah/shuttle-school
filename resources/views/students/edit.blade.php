@extends('layouts.admin')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Edit Data Siswa</div>
            <div class="card-body">
                <form action="{{ route('students.update', $student->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap Siswa</label>
                        <input type="text" class="form-control" name="name" value="{{ $student->name }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Orang Tua / Wali</label>
                            <select name="parent_id" class="form-select" required>
                                @foreach($parents as $parent)
                                    <option value="{{ $parent->id }}" {{ $student->parent_id == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Komplek Tempat Tinggal</label>
                            <select name="complex_id" class="form-select" required>
                                @foreach($complexes as $complex)
                                    <option value="{{ $complex->id }}" {{ $student->complex_id == $complex->id ? 'selected' : '' }}>
                                        {{ $complex->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Detail Alamat</label>
                        <input type="text" class="form-control" name="address_note" value="{{ $student->address_note }}">
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('students.index') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Update Data Siswa</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection