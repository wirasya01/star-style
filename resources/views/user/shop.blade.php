@extends('user.layout.layout')
@section('content')
    <!-- Breadcrumb Section Begin -->
    <section class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__text">
                        <h4>Shop</h4>
                        <div class="breadcrumb__links">
                            <a href="{{ url('/') }}">Home</a>
                            <span>Shop</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Breadcrumb Section End -->

    <!-- Shop Section Begin -->
    <section class="shop spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    <div class="shop__sidebar">
                        <div class="shop__sidebar__search">
<form action="{{ route('shop') }}" method="GET">
    <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}">
    <button type="submit"><span class="icon_search"></span></button>
</form>
                        </div>
                        <div class="shop__sidebar__accordion">
                            <div class="accordion" id="accordionExample">
                                <div class="card">
                                    <div class="card-heading">
                                        <a data-toggle="collapse" data-target="#collapseOne">Categories</a>
                                    </div>
                                    <div id="collapseOne" class="collapse show" data-parent="#accordionExample">
                                        <div class="card-body">
                                            <div class="shop__sidebar__categories">
                                                <ul class="nice-scroll">
                                                    @foreach ($kategori as $kat)
                                                        <li>
                                                            <a href="{{ route('shop', ['kategori_id' => $kat->id]) }}">
                                                                {{ $kat->nama }} ({{ $kat->produks->count() }})
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="shop__product__option">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="shop__product__option__left">
                                    <p>Showing 1–12 of 126 results</p>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="shop__product__option__right">
                                    <p>Sort by Price:</p>
                                    <select>
                                        <option value="">Low To High</option>
                                        <option value="">$0 - $55</option>
                                        <option value="">$55 - $100</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        @foreach ($produks as $produk)
                            <div class="col-lg-4 col-md-6 col-sm-6">
                                <div class="product__item">
                                    <div id="carousel-{{ $produk->id }}" class="carousel slide" data-ride="carousel">
                                        <div class="carousel-inner">
                                            @if ($produk->gambar && is_array(json_decode($produk->gambar, true)))
                                                @foreach (json_decode($produk->gambar, true) as $index => $image)
                                                    <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                                        <a href="{{ route('detailpesanan', ['id' => $produk->id]) }}">
                                                            <img src="{{ asset('storage/' . $image) }}"
                                                                alt="{{ $produk->nama }}" class="d-block w-100">
                                                        </a>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="carousel-item active">
                                                    <span>No Image</span>
                                                </div>
                                            @endif
                                        </div>
                                        @if ($produk->gambar && is_array(json_decode($produk->gambar)) && count(json_decode($produk->gambar)) > 1)
                                            <a class="carousel-control-prev" href="#carousel-{{ $produk->id }}"
                                                role="button" data-slide="prev">
                                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                <span class="sr-only">Previous</span>
                                            </a>
                                            <a class="carousel-control-next" href="#carousel-{{ $produk->id }}"
                                                role="button" data-slide="next">
                                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                <span class="sr-only">Next</span>
                                            </a>
                                        @endif
                                    </div>
                                    <div class="product__item__text">
                                        <h6>{{ $produk->nama }}</h6>
                                        <a href="#" class="add-cart" data-produk-id="{{ $produk->id }}">+ Add To
                                            Cart</a>
                                        <h5>Rp{{ number_format($produk->harga) }}</h5>
                                    </div>
                                    <a href="{{ route('detailpesanan', ['id' => $produk->id]) }}"
                                        class="btn btn-dark">Buy</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="product__pagination">
                                <a class="active" href="#">1</a>
                                <a href="#">2</a>
                                <a href="#">3</a>
                                <span>...</span>
                                <a href="#">21</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Shop Section End -->

    <!-- Hidden form for adding to cart -->
    <form id="add-to-cart-form" action="{{ route('keranjang.store') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="produk_id" id="produk-id">
        <input type="hidden" name="jumlah" value="1">
    </form>

    <!-- Modal -->
    <div class="modal fade" id="orderDetailModal" tabindex="-1" role="dialog" aria-labelledby="orderDetailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderDetailModalLabel">Order Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h6 id="modal-produk-nama"></h6>
                    <p id="modal-produk-harga"></p>
                    <p id="modal-produk-deskripsi"></p>
                    {{-- <form id="order-form" action="{{ route('') }}" method="GET">
                        <input type="hidden" name="produk_id" id="modal-produk-id">
                        <button type="submit" class="btn btn-primary">Proceed to Checkout</button>
                    </form> --}}
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.add-cart').forEach(function(button) {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    var produkId = this.getAttribute('data-produk-id');
                    document.getElementById('produk-id').value = produkId;
                    document.getElementById('add-to-cart-form').submit();
                });
            });

            document.querySelectorAll('.btn-buy').forEach(function(button) {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    var produkId = this.getAttribute('data-produk-id');
                    var produkNama = this.getAttribute('data-produk-nama');
                    var produkHarga = this.getAttribute('data-produk-harga');
                    var produkDeskripsi = this.getAttribute('data-produk-deskripsi');

                    document.getElementById('modal-produk-id').value = produkId;
                    document.getElementById('modal-produk-nama').innerText = produkNama;
                    document.getElementById('modal-produk-harga').innerText = produkHarga;
                    document.getElementById('modal-produk-deskripsi').innerText = produkDeskripsi;

                    $('#orderDetailModal').modal('show');
                });
            });
        });
    </script>
@endsection
