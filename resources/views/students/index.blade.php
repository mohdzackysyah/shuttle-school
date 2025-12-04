@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <h3 class="fw-bold text-dark">🎒 Data Siswa</h3>
        <p class="text-muted mb-0">Siswa yang terdaftar dalam layanan antar jemput.</p>
    </div>
    <a href="{{ route('students.create') }}" class="btn btn-primary shadow-sm">
        <i class="bi bi-plus-lg"></i> Tambah Siswa
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 py-3">Nama Siswa</th>
                        <th>Orang Tua / Wali</th>
                        <th>Lokasi Jemput</th>
                        <th>Detail Alamat</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                    <tr>
                        <td class="ps-4 fw-bold">
                            <div class="d-flex align-items-center">
                                {{-- Ikon Bulat --}}
                                <div class="bg-light rounded-circle p-2 me-3 text-primary d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    @if($student->photo)
                                        <img src="{{ asset('storage/'.$student->photo) }}" class="rounded-circle" style="width: 100%; height: 100%; object-fit: cover;">
                                    @else
                                        <i class="bi bi-backpack-fill fs-5"></i>
                                    @endif
                                </div>
                                {{-- Nama Siswa (Tanpa NIS) --}}
                                <div>
                                    {{ $student->name }}
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-semibold">{{ $student->parent->name ?? 'Belum ada' }}</span>
                                <span class="small text-muted"><i class="bi bi-telephone me-1"></i> {{ $student->parent->phone ?? '-' }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill border border-info border-opacity-25">
                                <i class="bi bi-building me-1"></i> {{ $student->complex->name ?? 'Umum' }}
                            </span>
                        </td>
                        <td>
                            <span class="text-muted small text-truncate d-inline-block" style="max-width: 150px;">
                                <i class="bi bi-geo-alt me-1"></i> {{ $student->address_note ?? '-' }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('students.edit', $student->id) }}" class="btn btn-sm btn-outline-warning me-1">
                                Edit
                            </a>
                            <form action="{{ route('students.destroy', $student->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data siswa ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <div class="mb-2"><i class="bi bi-emoji-frown fs-1"></i></div>
                            <h6>Belum ada data siswa.</h6>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection