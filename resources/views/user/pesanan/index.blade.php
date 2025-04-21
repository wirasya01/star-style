@extends('user.layout.layout')

@section('content')
<div class="container my-5">
    <h2 class="mb-4 fw-bold text-center">🛍️ Daftar Pesanan Saya</h2>

    @if($pesanans->isEmpty())
        <div class="alert alert-warning text-center">Anda belum memiliki pesanan.</div>
    @else
        @foreach($pesanans as $pesanan)
        <div class="card mb-4 shadow-sm border-0 rounded-4">
            <div class="card-header bg-white rounded-top-4 px-4 py-3 d-flex justify-content-between align-items-center border-bottom">
                <div>
                    <span class="fw-semibold text-secondary">No. Pesanan:</span> <strong>#{{ $pesanan->id }}</strong>
                    <span class="ms-3 text-muted"><i class="bi bi-calendar-event me-1"></i>{{ $pesanan->tanggal_pesan->format('d-m-Y H:i') }}</span>
                </div>
                <span class="badge py-2 px-3 rounded-pill
                    @if($pesanan->status == 'pending') bg-warning-subtle text-warning
                    @elseif($pesanan->status == 'diproses') bg-info-subtle text-info
                    @elseif($pesanan->status == 'selesai') bg-success-subtle text-success
                    @else bg-secondary-subtle text-secondary
                    @endif">
                    {{ ucfirst($pesanan->status) }}
                </span>
            </div>

            <div class="card-body px-4 pt-3">
                @foreach($pesanan->detailPesanans as $detail)
                    @php
                        $produk = $detail->produk;
                        $gambarArray = json_decode($produk->gambar, true);
                        $gambar = (is_array($gambarArray) && count($gambarArray) > 0) ? $gambarArray[0] : null;
                    @endphp
                    <div class="d-flex align-items-center mb-3 produk-item">
                        <img src="{{ $gambar ? asset('storage/' . $gambar) : asset('public/assets/img/hero/giordano1.png') }}"
                             alt="{{ $produk->nama }}"
                             class="rounded-3 me-3 transition"
                             style="width: 70px; height: 70px; object-fit: cover; transition: 0.3s ease;">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $produk->nama }}</h6>
                            <small class="text-muted">Jumlah: {{ $detail->jumlah }}</small>
                        </div>
                    </div>
                @endforeach

                <hr>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="fw-semibold text-muted">Total Harga:</div>
                    <div class="h5 text-danger">Rp{{ number_format($pesanan->total_harga, 0, ',', '.') }}</div>
                </div>
            </div>

            <div class="card-footer bg-white border-top-0 rounded-bottom-4 px-4 pb-3 text-end">
                <a href="{{ route('pesanan.show', $pesanan->id) }}" class="btn btn-danger btn-sm rounded-pill px-4 shadow-sm">
                    Lihat Detail
                </a>
            </div>
        </div>
        @endforeach
    @endif
</div>

<style>
    .produk-item img:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
</style>
@endsection
