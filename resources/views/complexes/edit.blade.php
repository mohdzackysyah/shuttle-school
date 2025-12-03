@extends('layouts.admin')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Edit Komplek</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('complexes.update', $complex->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Komplek</label>
                        <input type="text" class="form-control" name="name" value="{{ $complex->name }}" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Rute</label>
                        <select name="route_id" class="form-select" required>
                            @foreach($routes as $route)
                                <option value="{{ $route->id }}" {{ $complex->route_id == $route->id ? 'selected' : '' }}>
                                    {{ $route->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('complexes.index') }}" class="btn btn-light">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection