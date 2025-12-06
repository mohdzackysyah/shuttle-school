@extends('layouts.parent')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('parents.my_children') }}" class="btn btn-light rounded-circle shadow-sm me-3" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h4 class="fw-bold text-dark mb-0">Edit Data Anak</h4>
                    <p class="text-muted small mb-0">Perbarui foto dan detail alamat penjemputan.</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <form action="{{ route('parents.children.update', $student->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- FOTO PROFIL (CENTER) --}}
                        <div class="text-center mb-4">
                            <div class="position-relative d-inline-block">
                                @if($student->photo)
                                    <img src="{{ asset('storage/' . $student->photo) }}" alt="Foto Siswa" 
                                         class="rounded-circle shadow-sm border border-3 border-white" 
                                         style="width: 120px; height: 120px; object-fit: cover;" 
                                         id="photoPreview">
                                @else
                                    <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center text-primary fw-bold border border-3 border-white shadow-sm" 
                                         style="width: 120px; height: 120px; font-size: 2.5rem;" 
                                         id="photoPlaceholder">
                                        {{ substr($student->name, 0, 1) }}
                                    </div>
                                    <img src="" alt="Preview" class="rounded-circle shadow-sm border border-3 border-white d-none" 
                                         style="width: 120px; height: 120px; object-fit: cover;" 
                                         id="photoPreviewReal">
                                @endif

                                {{-- Tombol Kamera Overlay --}}
                                <label for="photoInput" class="position-absolute bottom-0 end-0 bg-white shadow-sm rounded-circle p-2 text-primary cursor-pointer border" style="cursor: pointer; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-camera-fill"></i>
                                </label>
                                <input type="file" name="photo" id="photoInput" class="d-none" accept="image/*" onchange="previewImage(this)">
                            </div>
                            <div class="mt-2 text-muted small">Ketuk ikon kamera untuk mengganti foto</div>
                        </div>

                        {{-- DATA READ-ONLY (Tidak Bisa Diedit) --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small text-uppercase">Nama Lengkap</label>
                            <input type="text" class="form-control bg-light" value="{{ $student->name }}" disabled readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small text-uppercase">Komplek Perumahan</label>
                            <input type="text" class="form-control bg-light" value="{{ $student->complex->name ?? '-' }}" disabled readonly>
                            <div class="form-text text-muted small"><i class="bi bi-info-circle me-1"></i> Hubungi admin jika ingin pindah komplek/rute.</div>
                        </div>

                        {{-- DATA YANG BISA DIEDIT --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-primary small text-uppercase">Detail Alamat / Patokan (Bisa Diedit)</label>
                            <textarea name="address_note" class="form-control" rows="3" placeholder="Contoh: Blok C No. 12, Pagar warna hitam...">{{ old('address_note', $student->address_note) }}</textarea>
                            <div class="form-text text-muted">Tuliskan nomor rumah, blok, atau ciri-ciri rumah agar mudah ditemukan driver.</div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary fw-bold py-3 rounded-pill shadow-sm">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
                // Sembunyikan placeholder inisial jika ada
                const placeholder = document.getElementById('photoPlaceholder');
                if(placeholder) placeholder.classList.add('d-none');

                // Tampilkan preview gambar
                const preview = document.getElementById('photoPreview') || document.getElementById('photoPreviewReal');
                preview.src = e.target.result;
                preview.classList.remove('d-none');
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection