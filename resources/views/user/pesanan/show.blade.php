@extends('user.layout.layout')

@section('content')
<div class="container mt-4">
    <h2>Detail Pesanan #{{ $pesanan->id }}</h2>
    <p><strong>Tanggal Pesan:</strong> {{ $pesanan->tanggal_pesan->format('d-m-Y H:i') }}</p>
    <p><strong>Status Pesanan:</strong> {{ ucfirst($pesanan->status) }}</p>

    <h4>Detail Produk</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Gambar</th>
                <th>Nama Produk</th>
                <th>Jumlah</th>
                <th>Ukuran</th>
                <th>Harga Satuan</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pesanan->detailPesanans as $detail)
            @php
                $gambarArray = json_decode($detail->produk->gambar, true);
                $gambar = (is_array($gambarArray) && count($gambarArray) > 0) ? $gambarArray[0] : null;
            @endphp
            <tr>
                <td>
                    @if($gambar)
                        <img src="{{ asset('storage/' . $gambar) }}" alt="{{ $detail->produk->nama }}" style="width: 50px; height: 50px; object-fit: cover;">
                    @else
                        <img src="{{ asset('public/assets/img/hero/giordano1.png') }}" alt="No Image" style="width: 50px; height: 50px; object-fit: cover;">
                    @endif
                </td>
                <td>{{ $detail->produk->nama }}</td>
                <td>{{ $detail->jumlah }}</td>
                <td>{{ $detail->ukuran }}</td>
                <td>Rp{{ number_format($detail->harga, 0, ',', '.') }}</td>
                <td>Rp{{ number_format($detail->harga * $detail->jumlah, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h4>Status Pembayaran</h4>
    @if($pesanan->pembayaran)
        <p><strong>Status:</strong> {{ ucfirst($pesanan->pembayaran->status) }}</p>
        <p><strong>Metode Pembayaran:</strong> {{ $pesanan->pembayaran->metode ?? 'N/A' }}</p>
        <p><strong>Tanggal Pembayaran:</strong> {{ $pesanan->pembayaran->tanggal_bayar ? $pesanan->pembayaran->tanggal_bayar->format('d-m-Y H:i') : 'N/A' }}</p>
    @else
        <p>Pembayaran belum dilakukan.</p>
    @endif

    <a href="{{ route('pesanan.index') }}" class="btn btn-secondary mt-3">Kembali ke Daftar Pesanan</a>
</div>
@endsection
