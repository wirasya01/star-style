@extends('user.layout.layout')

@section('content')
    <!-- Shop Details Section Begin -->
    <section class="shop-details">
        <div class="product__details__pic">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="product__details__breadcrumb text-center">
                            <a href="{{ route('home') }}">Home</a>
                            <a href="{{ route('shop') }}">Shop</a>
                            <span>Product Details</span>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <!-- Thumbnail Images -->
                    <div class="col-lg-3 col-md-3">
                        <ul class="nav nav-tabs d-flex flex-column align-items-center" role="tablist">
                            @foreach (json_decode($produk->gambar ?? '[]') as $index => $image)
                                <li class="nav-item">
                                    <a class="nav-link {{ $index == 0 ? 'active' : '' }}" data-toggle="tab"
                                        href="#tabs-{{ $index }}" role="tab">
                                        <div class="product__thumb__pic set-bg"
                                            data-setbg="{{ asset('storage/' . $image) }}">
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Main Image -->
                    <div class="col-lg-6 col-md-9 text-center">
                        <div class="tab-content">
                            @foreach (json_decode($produk->gambar ?? '[]') as $index => $image)
                                <div class="tab-pane {{ $index == 0 ? 'active' : '' }}" id="tabs-{{ $index }}"
                                    role="tabpanel">
                                    <div class="product__details__pic__item">
                                        <img src="{{ asset('storage/' . $image) }}" alt="{{ $produk->nama }}" id="mainImage"
                                            class="img-fluid rounded shadow">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Details -->
        <div class="product__details__content">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="product__details__text text-center">
                            <h4>{{ $produk->nama }}</h4>
                            <div class="rating">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star-o"></i>
                                <span> - 5 Reviews</span>
                            </div>
                            <h3>Rp{{ number_format($produk->harga, 0, ',', '.') }}</h3>
                            <p>{!! $produk->deskripsi !!}</p>

                            <!-- Size & Choose Image Section -->
                            <form action="{{ route('keranjang.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="produk_id" value="{{ $produk->id }}">
                                <div class="product__details__option text-center">
                                    <div class="d-flex justify-content-center align-items-center flex-wrap">
                                        <!-- Size Selection -->
                                        <div class="mr-4 text-center">
                                            <span class="d-block mb-1 font-weight-bold">Size:</span>
                                            <select name="ukuran" class="form-control mx-auto" style="width: 120px;">
                                                @foreach (json_decode($produk->ukuran ?? '[]') as $ukuran)
                                                    <option value="{{ $ukuran }}">{{ $ukuran }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Image Selection -->
                                        <div>
                                            <span class="d-block mb-1 font-weight-bold">Choose Image:</span>
                                            <div class="d-flex flex-wrap justify-content-center">
                                                @foreach (json_decode($produk->gambar ?? '[]') as $index => $gambar)
                                                    <label class="image-option-label m-2">
                                                        <input type="radio" name="gambar" value="{{ $gambar }}"
                                                            class="d-none">
                                                        <img src="{{ asset('storage/' . $gambar) }}"
                                                            alt="Image {{ $index }}"
                                                            class="image-option img-thumbnail"
                                                            style="width: 80px; height: 80px; border: 2px solid transparent; border-radius: 5px; cursor: pointer;">
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Cart Button -->
                                <div class="product__details__cart__option mt-3">
                                    <div class="quantity mx-auto d-inline-block">
                                        <div class="pro-qty">
                                            <input type="number" name="jumlah" value="1" min="1">
                                        </div>
                                    </div>
                                    <button type="submit" class="primary-btn">ADD TO CART</button>
                                </div>
                            </form>

                            <div class="product__details__btns__option mt-3">
                                <a href="#"><i class="fa fa-heart"></i> ADD TO WISHLIST</a>
                                <a href="#"><i class="fa fa-exchange"></i> ADD TO COMPARE</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Shop Details Section End -->

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.image-option-label').forEach(function(label) {
                label.addEventListener('click', function() {
                    document.querySelectorAll('.image-option').forEach(function(img) {
                        img.style.border = '2px solid transparent';
                    });
                    this.querySelector('.image-option').style.border = '2px solid #007bff';
                });
            });
        });
    </script>
    <style>
        .product__description {
            text-align: center;
            max-width: 600px;
            /* Atur lebar maksimal agar tidak terlalu melebar */
            margin: 0 auto;
            /* Pusatkan elemen */
        }

        .product__description ul {
            display: inline-block;
            /* Membuat daftar tetap dalam satu blok di tengah */
            text-align: left;
            /* Pastikan bullet tetap rata kiri */
        }
    </style>
@endsection
