@extends('user.layout.layout')

@section('content')
    <!-- Breadcrumb Section -->
    <section class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__text">
                        <h4>Detail Produk Keranjang</h4>
                        <div class="breadcrumb__links">
                            <a href="{{ route('home') }}">Home</a>
                            <a href="{{ route('keranjang.index') }}">Keranjang</a>
                            <span>Detail Produk</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Details Section -->
    <section class="product-details spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="product__details__pic">
                        @if ($keranjang->produk->gambar && is_array(json_decode($keranjang->produk->gambar, true)))
                            <img src="{{ asset('storage/' . json_decode($keranjang->produk->gambar, true)[0]) }}" 
                                 class="img-fluid rounded-3" 
                                 alt="{{ $keranjang->produk->nama }}"
                                 width="500">
                        @else
                            <div class="text-center py-5 bg-light">
                                <span>No Image Available</span>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="product__details__text">
                        <h3>{{ $keranjang->produk->nama }}</h3>
                        <div class="product__details__price">
                            Rp.{{ number_format($keranjang->produk->harga, 0, ',', '.') }}
                        </div>
                        <div class="product__details__widget">
                            <ul>
                                <li>
                                    <span>Ukuran:</span>
                                    <p>{{ $keranjang->ukuran }}</p>
                                </li>
                                <li>
                                    <span>Jumlah:</span>
                                    <p>{{ $keranjang->jumlah }}</p>
                                </li>
                                <li>
                                    <span>Subtotal:</span>
                                    <p>Rp.{{ number_format($keranjang->produk->harga * $keranjang->jumlah, 0, ',', '.') }}</p>
                                </li>
                            </ul>
                        </div>
                        <div class="product__details__button">
                            <a href="{{ route('keranjang.edit', $keranjang->id) }}" class="btn btn-warning">
                                <i class="fa fa-edit"></i> Edit Produk
                            </a>
                            <a href="{{ route('keranjang.index') }}" class="btn btn-outline-dark">
                                <i class="fa fa-arrow-left"></i> Kembali ke Keranjang
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
