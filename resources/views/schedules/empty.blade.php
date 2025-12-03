@extends('layouts.app')
@section('content')
<div class="alert alert-warning text-center">
    <h4>⚠️ Belum ada Semester yang Aktif</h4>
    <p>Silakan masuk ke menu <strong>Set Semester</strong> untuk membuat dan mengaktifkan Tahun Ajaran baru.</p>
    <a href="{{ route('academic-years.index') }}" class="btn btn-primary">Ke Menu Semester</a>
</div>
@endsection