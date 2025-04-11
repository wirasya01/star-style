@extends('admin.layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Detail Produk
                    <a href="{{ route('produk.index') }}" class="btn btn-sm btn-primary" style="float: right">Return</a>
                </div>
                <div class="card-body">
                    <!-- Nama Produk -->
                    <div class="mb-2">
                        <label for="">Nama Produk</label>
                        <input type="text" class="form-control" name="nama" value="{{ $produk->nama }}" disabled>
                    </div>

                    <!-- Deskripsi Produk -->
                    <div class="mb-2">
                        <label for="">Deskripsi</label>
                        <div class="form-control" style="height: auto;">{!! $produk->deskripsi !!}</div>
                    </div>

                    <!-- Stok Produk -->
                    <div class="mb-2">
                        <label for="">Stok</label>
                        <input type="number" class="form-control" name="stok" value="{{ $produk->stok }}" disabled>
                    </div>

                    <!-- Harga Produk -->
                    <div class="mb-2">
                        <label for="">Harga</label>
                        <input type="number" class="form-control" name="harga" value="{{ number_format($produk->harga, 0, ',', '.') }}" disabled>
                    </div>

                    <!-- Kategori Produk -->
                    <div class="mb-2">
                        <label for="">Kategori</label>
                        <input type="text" class="form-control" name="kategori_id" value="{{ $produk->kategori->nama }}" disabled>
                    </div>

                    <!-- Gambar Produk -->
                    <div class="mb-2">
                        <label for="">Gambar</label>
                        @if($produk->gambar && is_array(json_decode($produk->gambar)))
                            @foreach(json_decode($produk->gambar) as $image)
                                <img src="{{ asset('storage/' . $image) }}" alt="{{ $produk->nama }}" width="100" class="img-thumbnail">
                            @endforeach
                        @else
                            <span>No Image</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection