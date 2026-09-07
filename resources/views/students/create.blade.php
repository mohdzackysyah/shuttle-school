@extends('layouts.admin')

@section('content')
<form action="{{ route('students.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
    <div class="row g-4">
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-image me-2"></i>Foto Profil</h6>
                </div>
                <div class="card-body text-center p-4">
                    <div class="rounded-3 bg-light border border-dashed d-flex flex-column align-items-center justify-content-center mb-3" style="height: 200px;">
                        <i class="bi bi-person-bounding-box display-4 text-secondary opacity-50"></i>
                        <small class="text-muted mt-2">Preview Foto</small>
                    </div>
                    
                    <div class="text-start">
                        <label for="photo" class="form-label small fw-bold text-secondary">Upload Foto Siswa</label>
                        <input type="file" name="photo" id="photo" class="form-control form-control-sm" accept="image/*">
                        <div class="form-text text-xs mt-2">
                            <i class="bi bi-info-circle me-1"></i> Format: JPG/PNG. Maks: 2MB.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-person-lines-fill me-2"></i>Informasi Siswa</h6>
                    <a href="{{ route('students.index') }}" class="btn btn-sm btn-light text-muted border-0">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
                
                <div class="card-body p-3 p-md-4">
                    
                    <div class="form-floating mb-3">
                        <input type="text" name="name" class="form-control" id="name" placeholder="Nama Siswa" required>
                        <label for="name" class="text-secondary">Nama Lengkap Siswa</label>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select name="parent_id" class="form-select" id="parent_id" required>
                                    <option value="" selected disabled>Pilih Orang Tua</option>
                                    @foreach($parents as $parent)
                                        <option value="{{ $parent->id }}">{{ $parent->name }} ({{ $parent->phone }})</option>
                                    @endforeach
                                </select>
                                <label for="parent_id" class="text-secondary">Orang Tua / Wali</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <select name="complex_id" class="form-select" id="complex_id" required>
                                    <option value="" selected disabled>Pilih Komplek</option>
                                    @foreach($complexes as $complex)
                                        <option value="{{ $complex->id }}">{{ $complex->name }}</option>
                                    @endforeach
                                </select>
                                <label for="complex_id" class="text-secondary">Lokasi Jemputan</label>
                            </div>
                        </div>
                    </div>

                    <!-- Leaflet CSS -->
                    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
                    <style>
                        #map { height: 320px; width: 100%; border-radius: 12px; border: 1px solid #dee2e6; z-index: 1; }
                    </style>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="latitude" id="latitude" class="form-control bg-light" readonly placeholder="Latitude" required>
                                <label for="latitude" class="text-secondary">Latitude (Pilih di Peta)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="longitude" id="longitude" class="form-control bg-light" readonly placeholder="Longitude" required>
                                <label for="longitude" class="text-secondary">Longitude (Pilih di Peta)</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-bold"><i class="bi bi-map me-1"></i>Titik Penjemputan Peta</label>
                        <div class="input-group mb-2">
                            <input type="text" id="map-search-input" class="form-control" placeholder="Cari komplek, jalan, atau gedung...">
                            <button class="btn btn-secondary" type="button" id="map-search-btn">
                                <i class="bi bi-search"></i> Cari
                            </button>
                            <button class="btn btn-outline-primary" type="button" id="btn-current-location">
                                <i class="bi bi-geo-alt-fill"></i> Lokasi Saya
                            </button>
                        </div>
                        <div id="map" class="mb-2"></div>
                        <div class="form-text text-muted">
                            <i class="bi bi-info-circle me-1"></i> Geser penanda merah atau klik peta untuk menentukan titik koordinat yang presisi.
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="address_note" class="form-label text-secondary small fw-bold">Detail Alamat Lengkap / Patokan Rumah</label>
                        <textarea name="address_note" class="form-control" id="address_note" rows="3" placeholder="Contoh: Blok A5 No. 12, Pagar Hitam, Samping Warung"></textarea>
                    </div>

                    <div class="d-grid d-md-flex justify-content-md-end gap-2 pt-3 border-top">
                        <button type="reset" class="btn btn-light px-4 mb-2 mb-md-0">Reset</button>
                        <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm">
                            <i class="bi bi-save me-2"></i> Simpan Data
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Koordinat default (Pusat Jakarta/Indonesia)
        var defaultLat = -6.2088;
        var defaultLng = 106.8456;

        // Inisialisasi Peta
        var map = L.map('map').setView([defaultLat, defaultLng], 13);

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

        // Inisialisasi koordinat awal
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
            searchBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

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
                        alert('Lokasi tidak ditemukan. Coba ketik nama komplek atau kota secara spesifik.');
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Gagal mencari lokasi. Pastikan koneksi internet aktif.');
                })
                .finally(() => {
                    searchBtn.disabled = false;
                    searchBtn.innerHTML = '<i class="bi bi-search"></i> Cari';
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
                    locationBtn.innerHTML = '<i class="bi bi-geo-alt-fill"></i> Lokasi Saya';
                }, function(error) {
                    console.error(error);
                    alert('Gagal mendeteksi lokasi. Pastikan Anda mengizinkan akses lokasi di browser.');
                    locationBtn.disabled = false;
                    locationBtn.innerHTML = '<i class="bi bi-geo-alt-fill"></i> Lokasi Saya';
                });
            } else {
                alert('Browser Anda tidak mendukung fitur Geolocation.');
            }
        });
    });
</script>
@endsection