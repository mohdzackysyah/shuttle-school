@extends('layouts.parent')

@section('content')
<div class="container py-3">
    
    <div class="mb-4 text-center">
        <h6 class="text-muted text-uppercase ls-1 mb-2">Detail Perjalanan</h6>
        <h2 class="fw-bold text-dark mb-3">{{ \Carbon\Carbon::parse($passenger->trip->date)->format('d M Y') }}</h2>
        
        @if($passenger->status == 'pending')
            <span class="badge bg-warning text-dark px-4 py-2 rounded-pill fs-6 shadow-sm">⏳ MENUNGGU JEMPUTAN</span>
        @elseif($passenger->status == 'picked_up')
            <span class="badge bg-primary px-4 py-2 rounded-pill fs-6 shadow-sm">🚌 SEDANG DIJALAN</span>
        @elseif($passenger->status == 'dropped_off')
            <span class="badge bg-success px-4 py-2 rounded-pill fs-6 shadow-sm">✅ SUDAH SAMPAI</span>
        @elseif($passenger->status == 'absent')
            <span class="badge bg-danger px-4 py-2 rounded-pill fs-6 shadow-sm">❌ TIDAK HADIR</span>
        @endif
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="card-header border-0 py-3 bg-warning bg-opacity-10">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-person-vcard-fill fs-5 text-warning"></i>
                <h6 class="fw-bold mb-0 text-dark">Informasi Driver & Armada</h6>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-3 text-center">
                    @if($passenger->trip->driver->photo)
                        <img src="{{ asset('storage/' . $passenger->trip->driver->photo) }}" 
                             class="rounded-circle object-fit-cover shadow-sm" 
                             style="width: 65px; height: 65px; border: 3px solid white;">
                    @else
                        <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto shadow-sm" 
                             style="width: 65px; height: 65px; font-size: 1.5rem; border: 3px solid white;">
                            {{ substr($passenger->trip->driver->name, 0, 1) }}
                        </div>
                    @endif
                </div>
                
                <div class="col-9 ps-3">
                    <h5 class="fw-bold mb-1 text-dark">{{ $passenger->trip->driver->name }}</h5>
                    <div class="mb-2 text-muted small">Driver Resmi</div>
                    
                    <a href="https://wa.me/{{ $passenger->trip->driver->phone }}" target="_blank" class="btn btn-success btn-sm rounded-pill px-3 fw-bold shadow-sm">
                        <i class="bi bi-whatsapp me-1"></i> Chat WhatsApp
                    </a>
                </div>
            </div>

            <hr class="border-light my-4">

            <div class="row g-3">
                <div class="col-6 border-end">
                    <small class="text-muted d-block mb-1">KENDARAAN</small>
                    <div class="fw-bold text-dark fs-5">{{ $passenger->trip->shuttle->plate_number }}</div>
                    <div class="small text-secondary">{{ $passenger->trip->shuttle->car_model }}</div>
                </div>
                <div class="col-6 ps-4">
                    <small class="text-muted d-block mb-1">RUTE / TUJUAN</small>
                    <div class="fw-bold text-primary">{{ $passenger->trip->route->name }}</div>
                    <div class="small text-secondary">
                        {{ $passenger->trip->type == 'pickup' ? 'Menuju Sekolah' : 'Menuju Rumah' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Map Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" id="liveMapCard">
        <div class="card-header border-0 py-3 bg-primary bg-opacity-10">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-geo-fill fs-5 text-primary"></i>
                <h6 class="fw-bold mb-0 text-dark">Pelacakan Live Lokasi Driver</h6>
            </div>
        </div>
        <div class="card-body p-0">
            <div id="parentMap" style="height: 320px; width: 100%; position: relative;"></div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold text-dark mb-4">Kronologi Waktu</h5>
            
            <div class="d-flex mb-1 position-relative">
                <div class="me-3 text-center d-flex flex-column align-items-center" style="width: 45px;">
                    <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm
                        {{ $passenger->picked_at ? 'bg-primary text-white' : 'bg-light text-muted border' }}" 
                        style="width: 45px; height: 45px; flex-shrink: 0; z-index: 1;">
                        <i class="bi bi-box-arrow-in-right fs-5"></i>
                    </div>
                    <div class="{{ $passenger->dropped_at ? 'bg-success' : 'bg-secondary opacity-25' }}" style="width: 2px; height: 45px;"></div>
                </div>
                <div class="ps-1 pt-1 pb-3">
                    @if($passenger->picked_at)
                        <small class="text-success fw-bold d-flex align-items-center gap-1 mb-1">
                            <i class="bi bi-check-all fs-6"></i> Terkonfirmasi
                        </small>
                    @else
                        <small class="text-muted fw-bold d-block mb-1">WAKTU NAIK / JEMPUT</small>
                    @endif
                    <h3 class="fw-bold text-dark mb-1 fs-2">
                        {{ $passenger->picked_at ? \Carbon\Carbon::parse($passenger->picked_at)->format('H:i') : '-- : --' }} 
                        <span class="fs-6 text-muted fw-bold">WIB</span>
                    </h3>
                    @if($passenger->picked_at)
                        <small class="text-success fw-bold"><i class="bi bi-check-all"></i> Terkonfirmasi</small>
                    @else
                        <small class="text-secondary fst-italic">Belum dijemput</small>
                    @endif
                </div>
            </div>

            <div class="d-flex position-relative">
                <div class="me-3 text-center d-flex flex-column align-items-center" style="width: 45px;">
                    <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm
                        {{ $passenger->dropped_at ? 'bg-success text-white' : 'bg-light text-muted border' }}" 
                        style="width: 45px; height: 45px; flex-shrink: 0; z-index: 1;">
                        <i class="bi bi-geo-alt-fill fs-5"></i>
                    </div>
                </div>
                <div class="ps-1 pt-1">
                    <small class="text-muted fw-bold d-block mb-1">WAKTU TURUN / SAMPAI</small>
                    <h3 class="fw-bold text-dark mb-1 fs-2">
                        {{ $passenger->dropped_at ? \Carbon\Carbon::parse($passenger->dropped_at)->format('H:i') : '-- : --' }} 
                        <span class="fs-6 text-muted fw-bold">WIB</span>
                    </h3>
                    @if($passenger->dropped_at)
                        <small class="text-success fw-bold"><i class="bi bi-check-all"></i> Terkonfirmasi</small>
                    @else
                        <small class="text-secondary fst-italic">Belum sampai</small>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <div class="d-grid mb-5">
        <a href="{{ route('parents.dashboard') }}" class="btn btn-light py-3 rounded-3 fw-bold text-secondary shadow-sm border">
            <i class="bi bi-arrow-left me-2"></i> Kembali ke Dashboard
        </a>
    </div>

</div>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Koordinat awal (default ke titik jemput anak)
        var studentLat = {{ $passenger->student->latitude ?? -6.2088 }};
        var studentLng = {{ $passenger->student->longitude ?? 106.8456 }};
        
        var driverLat = {{ $passenger->trip->current_latitude ?? 'null' }};
        var driverLng = {{ $passenger->trip->current_longitude ?? 'null' }};

        // Inisialisasi Peta
        var map = L.map('parentMap').setView([studentLat, studentLng], 14);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; contributors'
        }).addTo(map);

        // Marker Rumah Siswa (Titik Penjemputan)
        const homeIcon = L.divIcon({
            html: '<div style="background-color: #ef4444; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 0 8px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; color: white;"><i class="bi bi-house-door-fill" style="font-size: 10px;"></i></div>',
            className: 'home-marker-icon',
            iconSize: [20, 20],
            iconAnchor: [10, 10]
        });
        L.marker([studentLat, studentLng], { icon: homeIcon }).addTo(map).bindPopup("<b>Rumah Anda</b>");

        // Marker Mobil Driver
        var driverMarker = null;
        const driverIcon = L.divIcon({
            html: '<div style="background-color: #3b82f6; width: 22px; height: 22px; border-radius: 50%; border: 3px solid white; box-shadow: 0 0 8px rgba(59,130,246,0.8); display: flex; align-items: center; justify-content: center; color: white;"><i class="bi bi-car-front-fill" style="font-size: 10px;"></i></div>',
            className: 'driver-marker-icon',
            iconSize: [22, 22],
            iconAnchor: [11, 11]
        });

        function updateDriverMarker(lat, lng) {
            if (lat && lng) {
                if (driverMarker) {
                    driverMarker.setLatLng([lat, lng]);
                } else {
                    driverMarker = L.marker([lat, lng], { icon: driverIcon }).addTo(map).bindPopup("<b>Posisi Driver (Mobil)</b>");
                }
                
                // Set view to encompass both markers
                var bounds = L.latLngBounds([[studentLat, studentLng], [lat, lng]]);
                map.fitBounds(bounds, { padding: [50, 50] });
            }
        }

        // Jalankan plot awal jika driver koordinat ada
        if (driverLat && driverLng) {
            updateDriverMarker(driverLat, driverLng);
        }

        // --- POLLING AJAX ---
        setInterval(function() {
            fetch(window.location.href, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // 1. Update status badge
                    const badgeContainer = document.querySelector('.mb-4.text-center .badge');
                    if (badgeContainer) {
                        if (data.passenger_status === 'pending') {
                            badgeContainer.className = 'badge bg-warning text-dark px-4 py-2 rounded-pill fs-6 shadow-sm';
                            badgeContainer.textContent = '⏳ MENUNGGU JEMPUTAN';
                        } else if (data.passenger_status === 'picked_up') {
                            badgeContainer.className = 'badge bg-primary px-4 py-2 rounded-pill fs-6 shadow-sm';
                            badgeContainer.textContent = '🚌 SEDANG DIJALAN';
                        } else if (data.passenger_status === 'dropped_off') {
                            badgeContainer.className = 'badge bg-success px-4 py-2 rounded-pill fs-6 shadow-sm';
                            badgeContainer.textContent = '✅ SUDAH SAMPAI';
                        } else if (data.passenger_status === 'absent') {
                            badgeContainer.className = 'badge bg-danger px-4 py-2 rounded-pill fs-6 shadow-sm';
                            badgeContainer.textContent = '❌ TIDAK HADIR';
                        }
                    }

                    // 2. Update Kronologi Waktu
                    const timeTexts = document.querySelectorAll('.ps-2 h4, .ps-2 h5');
                    const statusTexts = document.querySelectorAll('.ps-2 small');
                    
                    // picked_at update
                    if (data.picked_at && timeTexts[0]) {
                        timeTexts[0].className = 'fw-bold text-dark mb-0';
                        timeTexts[0].innerHTML = `${data.picked_at} <span class="fs-6 text-muted">WIB</span>`;
                        statusTexts[0].className = 'text-success';
                        statusTexts[0].innerHTML = '<i class="bi bi-check-all"></i> Terkonfirmasi';
                        
                        const iconCircles = document.querySelectorAll('.me-3 .rounded-circle');
                        if (iconCircles[0]) {
                            iconCircles[0].className = 'rounded-circle d-flex align-items-center justify-content-center mx-auto shadow-sm bg-primary text-white';
                        }
                    }

                    // dropped_at update
                    if (data.dropped_at && timeTexts[1]) {
                        timeTexts[1].className = 'fw-bold text-dark mb-0';
                        timeTexts[1].innerHTML = `${data.dropped_at} <span class="fs-6 text-muted">WIB</span>`;
                        statusTexts[1].className = 'text-success';
                        statusTexts[1].innerHTML = '<i class="bi bi-check-all"></i> Terkonfirmasi';
                        
                        const iconCircles = document.querySelectorAll('.me-3 .rounded-circle');
                        if (iconCircles[1]) {
                            iconCircles[1].className = 'rounded-circle d-flex align-items-center justify-content-center mx-auto shadow-sm bg-success text-white';
                        }
                    }

                    // 3. Update Peta Driver
                    updateDriverMarker(data.driver_lat, data.driver_lng);
                }
            })
            .catch(err => console.error("Error polling driver location:", err));
        }, 5000); // Poll setiap 5 detik
    });
</script>
@endsection