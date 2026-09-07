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

                        <!-- Leaflet CSS -->
                        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
                        <style>
                            #map { height: 300px; width: 100%; border-radius: 12px; border: 1px solid #dee2e6; z-index: 1; }
                        </style>

                        {{-- DATA LOKASI (LATITUDE & LONGITUDE) --}}
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label for="latitude" class="form-label fw-bold text-muted small text-uppercase">Latitude</label>
                                <input type="text" name="latitude" id="latitude" class="form-control bg-light" value="{{ old('latitude', $student->latitude) }}" readonly required placeholder="Klik di peta">
                            </div>
                            <div class="col-6">
                                <label for="longitude" class="form-label fw-bold text-muted small text-uppercase">Longitude</label>
                                <input type="text" name="longitude" id="longitude" class="form-control bg-light" value="{{ old('longitude', $student->longitude) }}" readonly required placeholder="Klik di peta">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-primary small text-uppercase"><i class="bi bi-map me-1"></i>Titik Penjemputan di Peta</label>
                            <div class="input-group mb-2">
                                <input type="text" id="map-search-input" class="form-control" placeholder="Cari nama jalan atau komplek...">
                                <button class="btn btn-secondary" type="button" id="map-search-btn">Cari</button>
                                <button class="btn btn-outline-primary" type="button" id="btn-current-location"><i class="bi bi-geo-alt-fill"></i></button>
                            </div>
                            <div id="map" class="mb-2"></div>
                            <div class="form-text text-muted small"><i class="bi bi-info-circle me-1"></i> Geser penanda merah atau klik peta pada atap rumah Anda untuk keakuratan penjemputan.</div>
                        </div>

                        {{-- DATA YANG BISA DIEDIT --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-primary small text-uppercase">Detail Alamat / Patokan Tambahan</label>
                            <textarea name="address_note" class="form-control" rows="3" placeholder="Contoh: Blok C No. 12, Pagar warna hitam...">{{ old('address_note', $student->address_note) }}</textarea>
                            <div class="form-text text-muted">Tuliskan nomor rumah, warna pagar, atau patokan agar mudah ditemukan driver.</div>
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

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
                const placeholder = document.getElementById('photoPlaceholder');
                if(placeholder) placeholder.classList.add('d-none');

                const preview = document.getElementById('photoPreview') || document.getElementById('photoPreviewReal');
                preview.src = e.target.result;
                preview.classList.remove('d-none');
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Koordinat default atau koordinat siswa saat ini
        var defaultLat = {{ $student->latitude ?? -6.2088 }};
        var defaultLng = {{ $student->longitude ?? 106.8456 }};
        var isLocationSet = {{ ($student->latitude && $student->longitude) ? 'true' : 'false' }};

        // Inisialisasi Peta
        var map = L.map('map').setView([defaultLat, defaultLng], isLocationSet ? 16 : 13);

        // Tambah Layer OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Tambah Marker yang bisa digeser
        var marker = L.marker([defaultLat, defaultLng], {
            draggable: true
        }).addTo(map);

        // Fungsi update input koordinat
        function updateCoordinates(lat, lng) {
            document.getElementById('latitude').value = lat.toFixed(8);
            document.getElementById('longitude').value = lng.toFixed(8);
        }

        // Inisialisasi koordinat
        updateCoordinates(defaultLat, defaultLng);

        // Jalankan update saat marker digeser
        marker.on('dragend', function(e) {
            var position = marker.getLatLng();
            updateCoordinates(position.lat, position.lng);
        });

        // Jalankan update saat peta diklik
        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            updateCoordinates(e.latlng.lat, e.latlng.lng);
        });

        // Logika Pencarian Alamat via Nominatim API (OpenStreetMap)
        var searchBtn = document.getElementById('map-search-btn');
        var searchInput = document.getElementById('map-search-input');

        function performSearch() {
            var query = searchInput.value;
            if (!query) return;

            searchBtn.disabled = true;
            searchBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';

            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        var lat = parseFloat(data[0].lat);
                        var lon = parseFloat(data[0].lon);
                        
                        map.setView([lat, lon], 16);
                        marker.setLatLng([lat, lon]);
                        updateCoordinates(lat, lon);
                    } else {
                        alert('Lokasi tidak ditemukan. Harap masukkan nama wilayah secara spesifik.');
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Gagal mencari lokasi.');
                })
                .finally(() => {
                    searchBtn.disabled = false;
                    searchBtn.innerHTML = 'Cari';
                });
        }

        searchBtn.addEventListener('click', performSearch);
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                performSearch();
            }
        });

        // Logika Dapatkan Lokasi Pengguna Saat Ini
        var locationBtn = document.getElementById('btn-current-location');
        locationBtn.addEventListener('click', function() {
            if (navigator.geolocation) {
                locationBtn.disabled = true;
                locationBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';

                navigator.geolocation.getCurrentPosition(function(position) {
                    var lat = position.coords.latitude;
                    var lng = position.coords.longitude;

                    map.setView([lat, lng], 16);
                    marker.setLatLng([lat, lng]);
                    updateCoordinates(lat, lng);

                    locationBtn.disabled = false;
                    locationBtn.innerHTML = '<i class="bi bi-geo-alt-fill"></i>';
                }, function(error) {
                    console.error(error);
                    alert('Gagal mendeteksi lokasi.');
                    locationBtn.disabled = false;
                    locationBtn.innerHTML = '<i class="bi bi-geo-alt-fill"></i>';
                });
            } else {
                alert('Browser Anda tidak mendukung fitur Geolocation.');
            }
        });
    });
</script>
@endsection