@@extends('layouts.admin')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Edit Wali Murid</div>
            <div class="card-body">
                <form action="{{ route('parents.update', $parent->id) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="mb-3"><label>Nama</label><input type="text" name="name" value="{{ $parent->name }}" class="form-control" required></div>
                    <div class="mb-3"><label>Email</label><input type="email" name="email" value="{{ $parent->email }}" class="form-control" required></div>
                    <div class="mb-3"><label>No HP</label><input type="text" name="phone" value="{{ $parent->phone }}" class="form-control" required></div>
                    <div class="mb-3"><label>Password (Opsional)</label><input type="password" name="password" class="form-control"></div>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('parents.index') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection