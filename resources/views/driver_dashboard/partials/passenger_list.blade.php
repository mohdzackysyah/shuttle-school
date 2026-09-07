@forelse($passengers as $p)
    @php
        // Logika Warna Status
        $stripeClass = 'stripe-pending';
        $cardBg = 'bg-white';
        if($p->status == 'waiting') { $stripeClass = 'stripe-waiting'; $cardBg = 'bg-waiting'; }
        elseif($p->status == 'picked_up') { $stripeClass = 'stripe-active'; if($trip->type != 'pickup') $cardBg = 'bg-active'; } 
        elseif($p->status == 'dropped_off') { $stripeClass = 'stripe-done'; $cardBg = 'bg-done'; }
        elseif($p->status == 'skipped') { $stripeClass = 'stripe-skip'; $cardBg = 'bg-skip'; }
    @endphp

    <div class="card-student p-3 {{ $cardBg }} passenger-item" 
         data-name="{{ $p->student->name }}"
         data-lat="{{ $p->student->latitude }}"
         data-lng="{{ $p->student->longitude }}"
         data-status="{{ $p->status }}"
         data-complex="{{ $p->student->complex->name ?? '-' }}"
         data-note="{{ $p->student->address_note ?? '' }}">
        <div class="status-stripe {{ $stripeClass }}"></div>
        
        {{-- INFO SISWA (KLIK UNTUK MODAL) --}}
        <div class="d-flex align-items-center mb-3 ps-2 clickable-area" 
             style="cursor: pointer;"
             data-bs-toggle="modal" 
             data-bs-target="#studentModal-{{ $p->id }}">
             
            <div class="me-3 position-relative">
                @if($p->student->photo)
                    <img src="{{ asset('storage/'.$p->student->photo) }}" class="rounded-circle shadow-sm" style="width: 50px; height: 50px; object-fit: cover;">
                @else
                    <div class="avatar-circle" style="width: 50px; height: 50px; font-size: 1.2rem;">
                        {{ substr($p->student->name, 0, 1) }}
                    </div>
                @endif
            </div>
            
            <div class="flex-grow-1" style="min-width: 0;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <h6 class="fw-bold text-dark mb-0 text-truncate">{{ $p->student->name }}</h6>
                    @if($p->status == 'waiting') <span class="badge bg-warning text-dark rounded-pill" style="font-size:0.6rem;">MENUNGGU</span>
                    @elseif($p->status == 'picked_up') <span class="badge bg-primary rounded-pill" style="font-size:0.6rem;">NAIK</span>
                    @elseif($p->status == 'dropped_off') <span class="badge bg-success rounded-pill" style="font-size:0.6rem;">SAMPAI</span>
                    @elseif($p->status == 'skipped') <span class="badge bg-danger rounded-pill" style="font-size:0.6rem;">SKIP</span>
                    @endif
                </div>
                <div class="text-muted small text-truncate">{{ $p->student->complex->name ?? 'Umum' }}</div>
            </div>
        </div>

        {{-- TOMBOL AKSI DENGAN VALIDASI SWEETALERT --}}
        <div class="ps-2">
            @if($p->status == 'pending')
                <div class="row g-2">
                    <div class="col-8">
                        @if($trip->type == 'pickup')
                            {{-- TOMBOL MENUNGGU (Jemputan) --}}
                            <form id="form-waiting-{{ $p->id }}" action="{{ route('driver.passenger.waiting', $p->id) }}" method="POST">
                                @csrf
                                <button type="button" onclick="confirmWaiting('{{ $p->id }}', '{{ $p->student->name }}')" class="btn-action btn-waiting">
                                    <i class="bi bi-geo-alt-fill fs-5"></i> SAMPAI TITIK
                                </button>
                            </form>
                        @else
                            {{-- TOMBOL NAIK (Antaran Sore - Langsung Naik) --}}
                            <form id="form-pickup-{{ $p->id }}" action="{{ route('driver.passenger.pickup', $p->id) }}" method="POST">
                                @csrf
                                <button type="button" onclick="confirmPickup('{{ $p->id }}', '{{ $p->student->name }}')" class="btn-action btn-pickup">
                                    <i class="bi bi-box-arrow-in-right fs-5"></i> SISWA NAIK
                                </button>
                            </form>
                        @endif
                    </div>
                    <div class="col-4">
                        <form id="form-skip-{{ $p->id }}" action="{{ route('driver.passenger.skip', $p->id) }}" method="POST">
                            @csrf
                            <button type="button" onclick="confirmSkip('{{ $p->id }}', '{{ $p->student->name }}')" class="btn-action btn-skip">SKIP</button>
                        </form>
                    </div>
                </div>

            @elseif($p->status == 'waiting')
                <div class="row g-2">
                    <div class="col-8">
                        <form id="form-pickup-{{ $p->id }}" action="{{ route('driver.passenger.pickup', $p->id) }}" method="POST">
                            @csrf
                            <button type="button" onclick="confirmPickup('{{ $p->id }}', '{{ $p->student->name }}')" class="btn-action btn-pickup">
                                <i class="bi bi-box-arrow-in-right fs-5"></i> SISWA NAIK
                            </button>
                        </form>
                    </div>
                    <div class="col-4">
                        <form id="form-skip-{{ $p->id }}" action="{{ route('driver.passenger.skip', $p->id) }}" method="POST">
                            @csrf
                            <button type="button" onclick="confirmSkip('{{ $p->id }}', '{{ $p->student->name }}')" class="btn-action btn-skip">SKIP</button>
                        </form>
                    </div>
                </div>

            @elseif($p->status == 'picked_up' && $trip->type != 'pickup') 
                <form id="form-dropoff-{{ $p->id }}" action="{{ route('driver.passenger.dropoff', $p->id) }}" method="POST">
                    @csrf
                    <button type="button" onclick="confirmDropoff('{{ $p->id }}', '{{ $p->student->name }}')" class="btn-action btn-dropoff">
                        <i class="bi bi-house-check-fill fs-5"></i> TURUN (SAMPAI)
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- MODAL DETAIL SISWA --}}
    <div class="modal fade" id="studentModal-{{ $p->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-bottom-0 pb-0 bg-white sticky-top">
                    <h5 class="modal-title fw-bold">Detail Lengkap Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    {{-- Konten Modal (Foto, Nama, dll) --}}
                    <div class="text-center mb-4">
                        @if($p->student->photo)
                            <img src="{{ asset('storage/'.$p->student->photo) }}" class="rounded-circle shadow mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                        @else
                            <div class="avatar-circle mx-auto shadow mb-3" style="width: 100px; height: 100px; font-size: 2.5rem;">{{ substr($p->student->name, 0, 1) }}</div>
                        @endif
                        <h3 class="fw-bold mb-0 text-dark">{{ $p->student->name }}</h3>
                    </div>
                    
                    {{-- Info Komplek --}}
                    <div class="detail-box">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <i class="bi bi-geo-alt-fill text-danger fs-3"></i>
                            <div>
                                <span class="detail-label">Alamat / Komplek</span>
                                <div class="detail-value">{{ $p->student->complex->name ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="bg-white border rounded p-2 text-muted small mt-2">
                            {{ $p->student->address_note ?? 'Tidak ada catatan alamat' }}
                        </div>
                        @if($p->student->latitude && $p->student->longitude)
                            <a href="https://www.google.com/maps/search/?api=1&query={{ $p->student->latitude }},{{ $p->student->longitude }}" target="_blank" class="btn btn-outline-danger w-100 btn-sm fw-bold mt-2">
                                <i class="bi bi-geo-alt me-1"></i> Buka Google Maps (Navigasi)
                            </a>
                        @endif
                    </div>

                    {{-- Info Wali Murid --}}
                    <div class="detail-box">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <i class="bi bi-people-fill text-primary fs-3"></i>
                            <div>
                                <span class="detail-label">Orang Tua</span>
                                <div class="detail-value">{{ $p->student->parent->name ?? '-' }}</div>
                            </div>
                        </div>
                        @if(!empty($p->student->parent->phone))
                            @php
                                $waNum = $p->student->parent->phone;
                                if(substr($waNum, 0, 1) == '0') $waNum = '62' . substr($waNum, 1);
                            @endphp
                            <a href="https://wa.me/{{ $waNum }}?text=Halo" target="_blank" class="btn btn-success w-100 btn-sm fw-bold text-white mt-2">
                                <i class="bi bi-whatsapp me-1"></i> Hubungi WhatsApp
                            </a>
                        @endif
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-secondary w-100 rounded-pill fw-bold" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    {{-- End Modal --}}

@empty
    <div class="text-center py-5">
        <i class="bi bi-people text-muted display-1 opacity-25"></i>
        <p class="text-muted mt-3">Tidak ada data penumpang.</p>
    </div>
@endforelse
