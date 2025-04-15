@extends('admin.layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Daftar Pembayaran</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Pesanan ID</th>
                <th>Metode Pembayaran</th>
                <th>Status Pembayaran</th>
                <th>Tanggal Pembayaran</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pembayarans as $pembayaran)
            <tr>
                <td>{{ $pembayaran->id }}</td>
                <td>{{ $pembayaran->pesanan_id }}</td>
                <td>{{ $pembayaran->metode_pembayaran }}</td>
                <td>{{ $pembayaran->status_pembayaran }}</td>
                <td>{{ $pembayaran->tanggal_pembayaran ? $pembayaran->tanggal_pembayaran->format('d-m-Y') : '-' }}</td>
                <td>
                    <a href="{{ route('admin.pembayaran.show', $pembayaran->id) }}" class="btn btn-primary btn-sm">Detail</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $pembayarans->links() }}
</div>
@endsection
