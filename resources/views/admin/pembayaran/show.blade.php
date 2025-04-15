@extends('admin.layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Detail Pembayaran</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            Informasi Pembayaran
        </div>
        <div class="card-body">
            <p><strong>ID Pembayaran:</strong> {{ $pembayaran->id }}</p>
            <p><strong>Pesanan ID:</strong> {{ $pembayaran->pesanan_id }}</p>
            <p><strong>Metode Pembayaran:</strong> {{ $pembayaran->metode_pembayaran }}</p>
            <p><strong>Status Pembayaran:</strong> {{ $pembayaran->status_pembayaran }}</p>
            <p><strong>Tanggal Pembayaran:</strong> {{ $pembayaran->tanggal_pembayaran ? $pembayaran->tanggal_pembayaran->format('d-m-Y') : '-' }}</p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            Update Status Pembayaran
        </div>
        <div class="card-body">
            <form action="{{ route('admin.pembayaran.update', $pembayaran->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="status_pembayaran">Status Pembayaran</label>
                    <select name="status_pembayaran" id="status_pembayaran" class="form-control" required>
                        <option value="Pending" {{ $pembayaran->status_pembayaran == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Lunas" {{ $pembayaran->status_pembayaran == 'Lunas' ? 'selected' : '' }}>Lunas</option>
                        <option value="Gagal" {{ $pembayaran->status_pembayaran == 'Gagal' ? 'selected' : '' }}>Gagal</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary mt-3">Update Status</button>
            </form>
        </div>
    </div>

    <a href="{{ route('admin.pembayaran.index') }}" class="btn btn-secondary">Kembali ke Daftar Pembayaran</a>
</div>
@endsection
