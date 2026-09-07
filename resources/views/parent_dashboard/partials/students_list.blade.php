<div class="row">
    @forelse($students as $student)
    <div class="col-12 mb-4">
        <div class="student-card">
            
            {{-- Student Header --}}
            <div class="student-header">
                <div class="d-flex align-items-center">
                    <div class="student-avatar me-3">
                        @if($student->photo)
                            <img src="{{ asset('storage/' . $student->photo) }}" alt="{{ $student->name }}">
                        @else
                            <div class="avatar-placeholder">
                                <i class="bi bi-person-fill"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="student-name mb-1">{{ $student->name }}</h4>
                        <div class="student-location">
                            <i class="bi bi-geo-alt-fill me-1"></i>
                            {{ $student->complex->name ?? 'Komplek Tidak Diketahui' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Student Body (Status Trip) --}}
            <div class="student-body">
                
                {{-- Desktop View --}}
                <div class="d-none d-md-block">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="trip-section border-end-desktop">
                                <h6 class="trip-title">
                                    <i class="bi bi-sunrise-fill me-2"></i>
                                    Penjemputan (Pagi)
                                </h6>
                                @include('parent_dashboard.partials.trip_status', [
                                    'tripData' => $student->trip_pagi, 
                                    'scheduleData' => $student->schedule_pagi ?? null,
                                    'type' => 'Pagi'
                                ])
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="trip-section">
                                <h6 class="trip-title trip-title-afternoon">
                                    <i class="bi bi-sunset-fill me-2"></i>
                                    Pengantaran (Sore)
                                </h6>
                                @include('parent_dashboard.partials.trip_status', [
                                    'tripData' => $student->trip_sore, 
                                    'scheduleData' => $student->schedule_sore ?? null,
                                    'type' => 'Sore'
                                ])
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Mobile View --}}
                <div class="d-md-none">
                    <ul class="nav nav-tabs nav-fill student-tabs mb-3" id="tabs-{{ $student->id }}" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active tab-trigger" 
                                    id="pagi-tab-{{ $student->id }}" 
                                    data-bs-toggle="tab" 
                                    data-bs-target="#pagi-{{ $student->id }}" 
                                    data-student-id="{{ $student->id }}"
                                    data-type="pagi"
                                    type="button" role="tab">
                                <i class="bi bi-sunrise-fill me-1"></i> Pagi
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link tab-trigger" 
                                    id="sore-tab-{{ $student->id }}" 
                                    data-bs-toggle="tab" 
                                    data-bs-target="#sore-{{ $student->id }}" 
                                    data-student-id="{{ $student->id }}"
                                    data-type="sore"
                                    type="button" role="tab">
                                <i class="bi bi-sunset-fill me-1"></i> Sore
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="pagi-{{ $student->id }}" role="tabpanel">
                            @include('parent_dashboard.partials.trip_status', [
                                'tripData' => $student->trip_pagi, 
                                'scheduleData' => $student->schedule_pagi ?? null,
                                'type' => 'Pagi'
                            ])
                        </div>
                        <div class="tab-pane fade" id="sore-{{ $student->id }}" role="tabpanel">
                            @include('parent_dashboard.partials.trip_status', [
                                'tripData' => $student->trip_sore, 
                                'scheduleData' => $student->schedule_sore ?? null,
                                'type' => 'Sore'
                            ])
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="empty-state">
            <div class="empty-icon"><i class="bi bi-people"></i></div>
            <h5 class="fw-bold text-dark mb-2">Data Anak Kosong</h5>
            <p class="text-muted mb-0">Anda belum terhubung dengan data siswa manapun.</p>
        </div>
    </div>
    @endforelse
</div>
