@extends('layouts.driver')

@section('content')
{{-- Library SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

<style>
    body { background-color: #f1f5f9; font-family: 'Poppins', sans-serif; }
    #driverMap { z-index: 1; border-radius: 0 0 16px 16px; }

    /* Pulsing Effect for Driver Marker */
    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(59, 130, 246, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(59, 130, 246, 0);
        }
    }
    .driver-pulse {
        animation: pulse 2s infinite;
        border-radius: 50%;
    }
    
    /* Driver Tooltip Style */
    .driver-tooltip {
        background-color: #3b82f6 !important;
        color: white !important;
        border: none !important;
        border-radius: 6px !important;
        font-family: 'Poppins', sans-serif !important;
        font-size: 11px !important;
        font-weight: 600 !important;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15) !important;
        padding: 4px 8px !important;
    }
    .driver-tooltip::before {
        border-top-color: #3b82f6 !important;
    }
    
    /* 1. Sticky Header */
    .sticky-header {
        position: sticky; top: 0; z-index: 1020;
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        border-bottom-left-radius: 20px;
        border-bottom-right-radius: 20px;
        margin-left: -12px; margin-right: -12px;
        padding: 1.25rem 1.5rem;
        transition: all 0.3s ease;
    }

    /* 2. Loading Bar Kecil (Indikator Update Background) */
    .update-indicator {
        position: absolute; top: 0; left: 0; width: 100%; height: 3px;
        background: transparent; overflow: hidden;
    }
    .update-indicator .bar {
        width: 100%; height: 100%; background: #3b82f6; 
        transform: translateX(-100%);
        animation: loading 1.5s infinite;
        display: none; /* Muncul hanya saat fetching data */
    }
    @keyframes loading {
        100% { transform: translateX(100%); }
    }

    /* 3. Card Siswa */
    .card-student {
        background: white; border: none; border-radius: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        margin-bottom: 1rem; position: relative; overflow: hidden;
        transition: transform 0.2s;
        border: 1px solid #f1f5f9;
    }
    .card-student:active { transform: scale(0.99); }

    /* Indikator Status (Garis Kiri) */
    .status-stripe { position: absolute; left: 0; top: 0; bottom: 0; width: 6px; }
    .stripe-pending { background: #cbd5e1; }
    .stripe-waiting { background: #eab308; } 
    .stripe-active { background: #f59e0b; } 
    .stripe-done { background: #10b981; }
    .stripe-skip { background: #ef4444; }

    /* Background Card */
    .bg-done { background-color: #f0fdf4; border-color: #bbf7d0; }
    .bg-skip { background-color: #fef2f2; border-color: #fecaca; opacity: 0.8; }
    .bg-active { background-color: #fffbeb; border-color: #fde68a; }
    .bg-waiting { background-color: #fffde7; border-color: #fef08a; }

    /* Avatar */
    .avatar-circle {
        width: 45px; height: 45px;
        background: #f1f5f9; color: #64748b;
        border-radius: 50%; font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem; border: 2px solid white;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    /* Tombol Aksi */
    .btn-action {
        width: 100%; border: none; border-radius: 10px;
        padding: 12px; font-weight: 700; font-size: 0.9rem;
        display: flex; align-items: center; justify-content: center; gap: 8px;
        text-transform: uppercase; letter-spacing: 0.5px;
        transition: 0.2s;
        cursor: pointer;
    }
    
    .btn-pickup { background: #f59e0b; color: white; box-shadow: 0 4px 10px rgba(245, 158, 11, 0.2); }
    .btn-waiting { background: #eab308; color: white; box-shadow: 0 4px 10px rgba(234, 179, 8, 0.2); }
    .btn-dropoff { background: #10b981; color: white; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2); }
    .btn-skip { background: white; color: #ef4444; border: 1px solid #fee2e2; }

    /* Tombol Selesai Header */
    .btn-finish-header {
        background: #1e293b; color: white;
        border-radius: 50px; padding: 0.6rem 1.5rem;
        font-weight: 600; font-size: 0.85rem;
        display: flex; align-items: center; gap: 0.5rem;
        box-shadow: 0 4px 12px rgba(30, 41, 59, 0.2);
        border: none;
        cursor: pointer;
    }

    /* Area Klik Info Siswa */
    .clickable-area { transition: background-color 0.2s; border-radius: 12px; }
    .clickable-area:active { background-color: rgba(0,0,0,0.03); }
    
    /* Style Modal */
    .detail-label { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #64748b; margin-bottom: 4px; display: block; }
    .detail-value { font-size: 1rem; font-weight: 600; color: #1e293b; }
    .detail-box { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1rem; margin-bottom: 1rem; }
</style>

{{-- Kalkulasi Progress (Hitung Awal) --}}
@php
    $total = $passengers->count();
    $done = $passengers->filter(function($p) {
        return $p->status != 'pending' && $p->status != 'waiting';
    })->count();
    $percent = $total > 0 ? ($done/$total)*100 : 0;
@endphp

<div class="container pb-5">

    {{-- 1. STICKY HEADER --}}
    <div class="sticky-header mb-4">
        {{-- Indikator loading halus (muncul saat update background) --}}
        <div class="update-indicator"><div class="bar" id="loadingIndicator"></div></div>

        {{-- Bagian Header yang akan di-refresh AJAX (ID: header-content) --}}
        <div id="header-content">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div style="flex: 1; min-width: 0; margin-right: 15px;">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        @if($trip->type == 'pickup')
                            <span class="badge bg-warning text-dark rounded-pill" style="font-size: 0.65rem;"><i class="bi bi-sun-fill me-1"></i> PAGI</span>
                        @else
                            <span class="badge bg-info text-white rounded-pill" style="font-size: 0.65rem;"><i class="bi bi-moon-fill me-1"></i> SORE</span>
                        @endif
                        <span class="text-secondary small fw-bold" id="realtimeClock">--:--</span>
                    </div>
                    <h5 class="fw-bold text-dark mb-0 text-truncate">{{ $trip->route->name ?? 'Nama Rute' }}</h5>
                </div>
                
                {{-- Form Selesai Perjalanan (Tombol Hitam) --}}
                <form id="form-finish-trip" action="{{ route('driver.trip.finish', $trip->id) }}" method="POST">
                    @csrf
                    <button type="button" onclick="confirmFinishTrip()" class="btn-finish-header">
                        <i class="bi bi-flag-fill text-warning"></i> Selesai
                    </button>
                </form>
            </div>

            {{-- Progress Bar --}}
            <div class="d-flex align-items-center gap-2 mt-2">
                <div class="progress flex-grow-1" style="height: 6px; border-radius: 10px; background: #f1f5f9;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percent }}%"></div>
                </div>
                <small class="fw-bold text-muted" style="font-size: 0.75rem;">{{ $done }}/{{ $total }} Siswa</small>
            </div>
        </div>
    </div>

    <!-- Map Card Container -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" id="driverMapCard">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom-0">
            <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-map-fill me-2"></i>Peta Perjalanan</h6>
            <div>
                <button class="btn btn-sm btn-outline-primary border me-2" type="button" id="recenterDriverBtn" onclick="recenterMapOnDriver()" style="display: none;">
                    <i class="bi bi-crosshair me-1"></i> Pusatkan ke Mobil
                </button>
                <button class="btn btn-sm btn-light border" type="button" id="toggleMapBtn" onclick="toggleDriverMap()">
                    <i class="bi bi-eye-slash-fill me-1"></i> Sembunyikan Peta
                </button>
            </div>
        </div>
        <div class="card-body p-0" id="mapCollapseBody">
            <div id="driverMap" style="height: 320px; width: 100%; position: relative;"></div>
        </div>
    </div>

    {{-- 2. LIST KARTU SISWA (ID: passenger-list-container untuk AJAX) --}}
    <div id="passenger-list-container" class="pb-5 mb-5">
        @include('driver_dashboard.partials.passenger_list')
    </div>
</div>

<script>
    // Helper function to submit form via AJAX
    function submitActionAjax(formId, successCallback) {
        const form = document.getElementById(formId);
        if (!form) return;

        const formData = new FormData(form);
        const actionUrl = form.action;

        Swal.fire({
            title: 'Memproses...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch(actionUrl, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': formData.get('_token')
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) throw new Error('Response error');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: data.message,
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
                if (successCallback) successCallback(data);
            } else {
                Swal.fire('Gagal', data.message || 'Terjadi kesalahan.', 'error');
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire('Gagal', 'Gagal menghubungi server.', 'error');
        });
    }

    // --- 1. VALIDASI SWEETALERT2 ---

    // Validasi Selesai Trip
    function confirmFinishTrip() {
        Swal.fire({
            title: 'Selesaikan Perjalanan?',
            text: "Semua siswa sudah diantar/jemput?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#1e293b',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Selesai!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                submitActionAjax('form-finish-trip', function(data) {
                    window.location.href = "{{ route('driver.dashboard') }}";
                });
            }
        });
    }

    // Validasi Sampai Titik (Waiting)
    function confirmWaiting(id, name) {
        Swal.fire({
            title: 'Sudah di Lokasi?',
            text: "Konfirmasi sampai jemputan: " + name,
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#eab308',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Sampai',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                submitActionAjax('form-waiting-' + id, function() {
                    refreshTripData();
                });
            }
        });
    }

    // Validasi Siswa Naik (Pickup)
    function confirmPickup(id, name) {
        Swal.fire({
            title: 'Siswa Naik?',
            text: name + " sudah masuk ke mobil?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Naik',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                submitActionAjax('form-pickup-' + id, function() {
                    refreshTripData();
                });
            }
        });
    }

    // Validasi Siswa Turun (Dropoff)
    function confirmDropoff(id, name) {
        Swal.fire({
            title: 'Siswa Turun?',
            text: name + " sudah sampai tujuan?",
            icon: 'success',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Selesai',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                submitActionAjax('form-dropoff-' + id, function() {
                    refreshTripData();
                });
            }
        });
    }

    // Validasi Skip
    function confirmSkip(id, name) {
        Swal.fire({
            title: 'Lewati Siswa?',
            text: "Anda yakin melewati " + name + "?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Lewati',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                submitActionAjax('form-skip-' + id, function() {
                    refreshTripData();
                });
            }
        });
    }

    // --- 2. LOGIC AUTO REFRESH (AJAX) ---
    let refreshTripData;

    document.addEventListener('DOMContentLoaded', function() {
        const AUTO_REFRESH_INTERVAL = 5000; // 5 Detik

        // Clock Update
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }).replace(/\./g, ':');
            const el = document.getElementById('realtimeClock');
            if(el) el.textContent = timeString;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // AJAX Refresh Logic
        refreshTripData = function() {
            // Cek kondisi agar tidak ganggu user
            if(document.querySelector('.modal.show')) return; // Jika modal buka, jangan refresh
            if(Swal.isVisible()) return; // Jika alert buka, jangan refresh

            const loadingBar = document.getElementById('loadingIndicator');
            if(loadingBar) loadingBar.style.display = 'block';

            // Ambil data trip via AJAX
            fetch(window.location.href, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Refresh error');
                return response.json();
            })
            .then(data => {
                // 1. Update List Siswa
                const currentList = document.getElementById('passenger-list-container');
                if(data.html && currentList) {
                    currentList.innerHTML = data.html;
                    // Redraw map markers with updated statuses
                    if (typeof updateDriverMapMarkers === 'function') {
                        updateDriverMapMarkers();
                    }
                }

                // 2. Update Progress Bar
                const progressBar = document.querySelector('.progress-bar');
                if (progressBar) {
                    progressBar.style.width = data.percent + '%';
                }

                // Update text count
                const progressText = document.querySelector('.sticky-header small') || document.querySelector('.progress + small');
                if (progressText) {
                    progressText.textContent = data.done + '/' + data.total + ' Siswa';
                }
            })
            .catch(err => console.error('Auto refresh error:', err))
            .finally(() => {
                if(loadingBar) loadingBar.style.display = 'none';
            });
        };

        // Poll at interval
        setInterval(refreshTripData, AUTO_REFRESH_INTERVAL);
    });
</script>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    var map;
    var markersLayer = L.layerGroup();
    var driverMarker = null;
    var followDriver = true;
    
    // Routing variables
    var routeLine = null;
    var routeOutline = null;
    var lastRouteFetchTime = 0;

    // Toggle Map visibility
    function toggleDriverMap() {
        const mapCardBody = document.getElementById('mapCollapseBody');
        const btn = document.getElementById('toggleMapBtn');
        if (mapCardBody.style.display === 'none') {
            mapCardBody.style.display = 'block';
            btn.innerHTML = '<i class="bi bi-eye-slash-fill me-1"></i> Sembunyikan Peta';
            if (map) {
                setTimeout(() => map.invalidateSize(), 100);
            }
        } else {
            mapCardBody.style.display = 'none';
            btn.innerHTML = '<i class="bi bi-map-fill me-1"></i> Tampilkan Peta';
        }
    }

    // Recenter map on driver car
    function recenterMapOnDriver() {
        if (driverMarker && map) {
            map.setView(driverMarker.getLatLng(), 16);
            followDriver = true;
            // Force route update
            var latlng = driverMarker.getLatLng();
            forceRedrawRoute(latlng.lat, latlng.lng);
        }
    }

    // Draw route using OSRM API
    function drawRoute(fromLat, fromLng, toLat, toLng) {
        var osrmUrl = `https://router.project-osrm.org/route/v1/driving/${fromLng},${fromLat};${toLng},${toLat}?overview=full&geometries=geojson`;
        
        fetch(osrmUrl)
            .then(response => response.json())
            .then(data => {
                if (data.routes && data.routes.length > 0) {
                    var routeCoords = data.routes[0].geometry.coordinates.map(coord => [coord[1], coord[0]]);
                    
                    // Draw outer border (glow)
                    if (routeOutline) {
                        routeOutline.setLatLngs(routeCoords);
                    } else {
                        routeOutline = L.polyline(routeCoords, {
                            color: '#2563eb',
                            weight: 10,
                            opacity: 0.35,
                            lineCap: 'round',
                            lineJoin: 'round'
                        }).addTo(map);
                    }

                    // Draw inner core
                    if (routeLine) {
                        routeLine.setLatLngs(routeCoords);
                    } else {
                        routeLine = L.polyline(routeCoords, {
                            color: '#3b82f6',
                            weight: 5,
                            opacity: 0.9,
                            lineCap: 'round',
                            lineJoin: 'round'
                        }).addTo(map);
                    }
                }
            })
            .catch(err => console.error("OSRM Routing error:", err));
    }

    // Remove route lines from map
    function removeRouteLines() {
        if (routeLine) {
            map.removeLayer(routeLine);
            routeLine = null;
        }
        if (routeOutline) {
            map.removeLayer(routeOutline);
            routeOutline = null;
        }
    }

    // Logic to fetch route to the first pending or waiting passenger
    function updateRoutingPath(driverLat, driverLng) {
        if (!map) return;

        // Find the first passenger that is waiting or pending
        const nextPassenger = document.querySelector('.passenger-item[data-status="pending"], .passenger-item[data-status="waiting"]');
        
        if (nextPassenger) {
            const targetLat = parseFloat(nextPassenger.getAttribute('data-lat'));
            const targetLng = parseFloat(nextPassenger.getAttribute('data-lng'));

            if (!isNaN(targetLat) && !isNaN(targetLng)) {
                var now = Date.now();
                if (now - lastRouteFetchTime > 15000) { // Limit calls to once per 15s
                    lastRouteFetchTime = now;
                    drawRoute(driverLat, driverLng, targetLat, targetLng);
                }
                return;
            }
        }

        // If no target passenger left (all dropped/skipped), remove line
        removeRouteLines();
    }

    // Force redraw route line immediately (e.g. after AJAX refresh or recentering)
    function forceRedrawRoute(driverLat, driverLng) {
        const nextPassenger = document.querySelector('.passenger-item[data-status="pending"], .passenger-item[data-status="waiting"]');
        if (nextPassenger) {
            const targetLat = parseFloat(nextPassenger.getAttribute('data-lat'));
            const targetLng = parseFloat(nextPassenger.getAttribute('data-lng'));

            if (!isNaN(targetLat) && !isNaN(targetLng)) {
                drawRoute(driverLat, driverLng, targetLat, targetLng);
                return;
            }
        }
        removeRouteLines();
    }

    // Function to generate custom colorful icons for marker status
    function getStatusIcon(status) {
        let color = '#64748b'; // default pending: grey
        if (status === 'waiting') color = '#eab308'; // waiting: yellow
        else if (status === 'picked_up') color = '#2563eb'; // picked_up: blue
        else if (status === 'dropped_off') color = '#10b981'; // dropped_off: green
        else if (status === 'skipped') color = '#ef4444'; // skipped: red

        return L.divIcon({
            html: `<div style="background-color: ${color}; width: 16px; height: 16px; border-radius: 50%; border: 3.5px solid white; box-shadow: 0 0 6px rgba(0,0,0,0.35);"></div>`,
            className: 'custom-status-icon',
            iconSize: [16, 16],
            iconAnchor: [8, 8]
        });
    }

    // Function to update/plot passenger markers from DOM data attributes
    function updateDriverMapMarkers() {
        if (!map) return;

        markersLayer.clearLayers();
        var bounds = [];

        const passengerItems = document.querySelectorAll('.passenger-item');
        passengerItems.forEach(item => {
            const lat = parseFloat(item.getAttribute('data-lat'));
            const lng = parseFloat(item.getAttribute('data-lng'));
            const name = item.getAttribute('data-name');
            const status = item.getAttribute('data-status');
            const complex = item.getAttribute('data-complex');
            const note = item.getAttribute('data-note') || 'Tidak ada catatan alamat';

            if (!isNaN(lat) && !isNaN(lng)) {
                const latlng = [lat, lng];
                bounds.push(latlng);

                // Build status text
                let statusBadge = '<span class="badge bg-secondary">MENUNGGU</span>';
                if (status === 'waiting') statusBadge = '<span class="badge bg-warning text-dark">MENUNGGU DRIVER</span>';
                else if (status === 'picked_up') statusBadge = '<span class="badge bg-primary">DI MOBIL</span>';
                else if (status === 'dropped_off') statusBadge = '<span class="badge bg-success">SAMPAI</span>';
                else if (status === 'skipped') statusBadge = '<span class="badge bg-danger">SKIP</span>';

                // Popup content
                const popupContent = `
                    <div style="font-family: 'Poppins', sans-serif; min-width: 150px; font-size: 12px;">
                        <h6 style="margin-bottom: 2px; font-weight: 700; font-size: 13px;">${name}</h6>
                        <div style="margin-bottom: 6px;">${statusBadge}</div>
                        <p style="font-size: 11px; color: #64748b; margin-bottom: 8px; line-height: 1.4;">
                            <strong>Komplek:</strong> ${complex}<br>
                            <strong>Patokan:</strong> ${note}
                        </p>
                        <a href="https://www.google.com/maps/search/?api=1&query=${lat},${lng}" target="_blank" class="btn btn-danger btn-sm text-white w-100 fw-bold" style="font-size: 10px; padding: 4px 8px;">
                            <i class="bi bi-geo-alt-fill"></i> Mulai Navigasi
                        </a>
                    </div>
                `;

                L.marker(latlng, { icon: getStatusIcon(status) })
                    .bindPopup(popupContent)
                    .addTo(markersLayer);
            }
        });

        markersLayer.addTo(map);

        // Zoom map to fit all passenger markers on first load (if not actively following driver)
        if (bounds.length > 0 && !driverMarker) {
            map.fitBounds(bounds, { padding: [40, 40] });
        }

        // Force route line recalculation on marker status updates
        if (driverMarker) {
            var driverLatLng = driverMarker.getLatLng();
            forceRedrawRoute(driverLatLng.lat, driverLatLng.lng);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Map
        map = L.map('driverMap').setView([-6.2088, 106.8456], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; contributors'
        }).addTo(map);

        // Disable follow when user drags map manually
        map.on('dragstart', function() {
            followDriver = false;
        });

        // Initial plot
        updateDriverMapMarkers();

        // Track driver current position using GPS
        if (navigator.geolocation) {
            navigator.geolocation.watchPosition(function(position) {
                var lat = position.coords.latitude;
                var lng = position.coords.longitude;

                const driverIcon = L.divIcon({
                    html: `<div class="driver-pulse" style="background-color: #3b82f6; width: 24px; height: 24px; border: 3px solid white; display: flex; align-items: center; justify-content: center; color: white;"><i class="bi bi-car-front-fill" style="font-size: 11px;"></i></div>`,
                    className: 'driver-live-icon',
                    iconSize: [24, 24],
                    iconAnchor: [12, 12]
                });

                // Show recenter button
                const recenterBtn = document.getElementById('recenterDriverBtn');
                if (recenterBtn) recenterBtn.style.display = 'inline-block';

                if (driverMarker) {
                    driverMarker.setLatLng([lat, lng]);
                } else {
                    driverMarker = L.marker([lat, lng], { icon: driverIcon }).addTo(map);
                    driverMarker.bindTooltip("<b>Anda (Mobil Jemputan)</b>", {
                        permanent: true,
                        direction: 'top',
                        offset: [0, -10],
                        className: 'driver-tooltip'
                    }).openTooltip();
                }

                if (followDriver) {
                    map.setView([lat, lng], map.getZoom() > 14 ? map.getZoom() : 16);
                }

                // Update route line to next passenger
                updateRoutingPath(lat, lng);

                // Send live location coordinates to server DB via AJAX
                fetch(`/driver/trip/{{ $trip->id }}/location`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        latitude: lat,
                        longitude: lng
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) console.warn("Failed to update driver live location: ", data.message);
                })
                .catch(err => console.error("Error sending driver location: ", err));

            }, function(error) {
                console.error("WatchPosition GPS error: ", error);
            }, {
                enableHighAccuracy: true,
                maximumAge: 10000,
                timeout: 10000
            });
        }
    });
</script>
@endsection