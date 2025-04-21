{{-- filepath: /Users/rasyap.s/UJIKOM/star-style/resources/views/admin/pesanan/index.blade.php --}}
@extends('layouts.admin')

@section('content')
    <div class="container">
        <h1>Daftar Pesanan</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama User</th>
                    <th>Produk</th>
                    <th>Jumlah</th>
                    <th>Total Harga</th>
                    <th>Status Pembayaran</th>
                    <th>Tanggal Pesan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pesanans as $key => $pesanan)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $pesanan->pembeli->name }}</td>
                        <td>
                            <ul>
                                @foreach ($pesanan->detailPesanans as $detail)
                                    <li>{{ $detail->produk->nama_produk }} ({{ $detail->jumlah }}x)</li>
                                @endforeach
                            </ul>
                        </td>
                        <td>{{ $pesanan->detailPesanans->sum('jumlah') }}</td>
                        <td>Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</td>
                        <td>
                            @if ($pesanan->pembayaran && $pesanan->pembayaran->status == 'paid')
                                <span class="badge bg-success">Sudah Bayar</span>
                            @else
                                <span class="badge bg-danger">Belum Bayar</span>
                            @endif
                        </td>
                        <td>{{ $pesanan->tanggal_pesan->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada pesanan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
