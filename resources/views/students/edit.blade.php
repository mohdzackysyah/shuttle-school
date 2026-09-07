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

                    <!-- Leaflet CSS -->
                    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
                    <style>
                        #map { height: 320px; width: 100%; border-radius: 12px; border: 1px solid #dee2e6; z-index: 1; }
                    </style>

                    <div class="col-md-6">
                        <label for="latitude" class="form-label fw-bold">Latitude (Pilih di Peta)</label>
                        <input type="text" name="latitude" id="latitude" class="form-control bg-light" value="{{ old('latitude', $student->latitude) }}" readonly required placeholder="Klik peta untuk koordinat">
                    </div>
                    
                    <div class="col-md-6">
                        <label for="longitude" class="form-label fw-bold">Longitude (Pilih di Peta)</label>
                        <input type="text" name="longitude" id="longitude" class="form-control bg-light" value="{{ old('longitude', $student->longitude) }}" readonly required placeholder="Klik peta untuk koordinat">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold"><i class="bi bi-map me-1"></i>Titik Penjemputan Peta</label>
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
                        <div class="form-text text-muted mb-3">
                            <i class="bi bi-info-circle me-1"></i> Geser penanda merah atau klik peta untuk memperbarui titik koordinat yang presisi.
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold">Detail Alamat / Patokan Rumah</label>
                        <textarea name="address_note" class="form-control" rows="2" placeholder="Contoh: Blok A5 No. 12, Pagar Hitam, Samping Warung">{{ old('address_note', $student->address_note) }}</textarea>
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

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
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

        // Inisialisasi koordinat awal jika belum diset oleh database sebelumnya
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