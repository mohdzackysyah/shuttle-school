@extends('layouts.admin')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Edit Rute</div>
            <div class="card-body">
                <form action="{{ route('routes.update', $route->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="form-label">Nama Rute</label>
                        <input type="text" class="form-control" name="name" value="{{ $route->name }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Komplek yang Dilewati:</label>
                        <div class="row">
                            @foreach($complexes as $complex)
                            <div class="col-md-4 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="complexes[]" 
                                           value="{{ $complex->id }}" 
                                           id="c_{{ $complex->id }}"
                                           {{ $route->complexes->contains($complex->id) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="c_{{ $complex->id }}">
                                        {{ $complex->name }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('routes.index') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Update Rute</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection