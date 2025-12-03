@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">Buat Semester Baru</div>
            <div class="card-body">
                <form action="{{ route('academic-years.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label>Nama Semester</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Ganjil 2025" required>
                    </div>
                    <button class="btn btn-primary w-100">Simpan</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Daftar Semester</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead><tr><th>Nama</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @foreach($years as $y)
                        <tr class="{{ $y->is_active ? 'table-success' : '' }}">
                            <td>{{ $y->name }}</td>
                            <td>{!! $y->is_active ? '<b>AKTIF ✅</b>' : 'Non-Aktif' !!}</td>
                            <td>
                                @if(!$y->is_active)
                                    <form action="{{ route('academic-years.activate', $y->id) }}" method="POST" class="d-inline">
                                        @csrf <button class="btn btn-success btn-sm">Aktifkan</button>
                                    </form>
                                @endif
                                <form action="{{ route('academic-years.destroy', $y->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus?')">
                                    @csrf @method('DELETE') <button class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection