@extends('user.layout.layout')

@section('content')
    <!-- Breadcrumb Section -->
    <section class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__text">
                        <h4>Edit Produk Keranjang</h4>
                        <div class="breadcrumb__links">
                            <a href="{{ route('home') }}">Home</a>
                            <a href="{{ route('keranjang.index') }}">Keranjang</a>
                            <span>Edit Produk</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Edit Product Section -->
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
                        
                        <form action="{{ route('keranjang.update', $keranjang->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="product__details__widget">
                                <ul>
                                    <li>
                                        <span>Ukuran:</span>
                                        <select name="ukuran" class="form-select">
                                            <option value="S" {{ $keranjang->ukuran == 'S' ? 'selected' : '' }}>S</option>
                                            <option value="M" {{ $keranjang->ukuran == 'M' ? 'selected' : '' }}>M</option>
                                            <option value="L" {{ $keranjang->ukuran == 'L' ? 'selected' : '' }}>L</option>
                                            <option value="XL" {{ $keranjang->ukuran == 'XL' ? 'selected' : '' }}>XL</option>
                                        </select>
                                    </li>
                                    <li>
                                        <span>Jumlah:</span>
                                        <input type="number" name="jumlah" value="{{ $keranjang->jumlah }}" 
                                               min="1" class="form-control">
                                    </li>
                                </ul>
                            </div>
                            
                            <div class="product__details__button">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Simpan Perubahan
                                </button>
                                <a href="{{ route('keranjang.show', $keranjang->id) }}" class="btn btn-outline-secondary">
                                    <i class="fa fa-times"></i> Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
