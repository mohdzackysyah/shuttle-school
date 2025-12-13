@extends('layouts.admin')

@section('content')
<div class="container-fluid px-0" style="max-width: 800px;">
    <div class="mb-4">
        <a href="{{ route('announcements.index') }}" class="text-decoration-none text-muted small"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
        <h3 class="fw-bold text-dark mt-2">Buat Pengumuman Baru</h3>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <form action="{{ route('announcements.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold small text-uppercase text-secondary">Judul Pengumuman</label>
                    <input type="text" name="title" class="form-control bg-light border-0 py-3" placeholder="Contoh: Perubahan Jadwal Penjemputan" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-uppercase text-secondary">Target Penerima</label>
                    <select name="target_role" class="form-select bg-light border-0 py-3" required>
                        <option value="all">Semua (Driver & Wali Murid)</option>
                        <option value="driver">Hanya Driver</option>
                        <option value="parent">Hanya Wali Murid</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold small text-uppercase text-secondary">Isi Pengumuman</label>
                    <textarea name="content" rows="5" class="form-control bg-light border-0 py-3" placeholder="Tulis informasi detail di sini..." required></textarea>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-3 shadow-sm">Terbitkan Pengumuman</button>
            </form>
        </div>
    </div>
</div>
@endsection