@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{{ $pageTitle }}</h5>
        
        @if($type == 'driver')
            <a href="{{ route('users.create_driver') }}" class="btn btn-primary btn-sm">+ Tambah Driver Baru</a>
        @elseif($type == 'parent')
            <a href="{{ route('users.create_parent') }}" class="btn btn-success btn-sm">+ Tambah Wali Murid</a>
        @endif
    </div>
    <div class="card-body">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th width="50">No</th>
                    <th>Nama Lengkap</th>
                    <th>Email (Login)</th>
                    <th>No. WhatsApp</th>
                    
                    @if($type == 'driver')
                        <th>Nomor SIM</th>
                    @endif
                    
                    <th width="150">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $index => $user)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->phone }}</td>
                    
                    @if($type == 'driver')
                        <td>
                            {{ $user->driverProfile->license_number ?? '-' }}
                        </td>
                    @endif
                    
                    <td>
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ $type == 'driver' ? 6 : 5 }}" class="text-center">
                        Belum ada data {{ $type == 'driver' ? 'driver' : 'wali murid' }}.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection