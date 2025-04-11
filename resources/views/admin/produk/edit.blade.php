@extends('admin.layouts.app')

@section('page-title', 'Edit Produk')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Edit Produk
                        <a href="{{ route('produk.index') }}" class="btn btn-sm btn-primary" style="float: right">Return</a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('produk.update', $produk->id) }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="mb-2">
                                <label for="nama">Nama Produk</label>
                                <input type="text" class="form-control @error('nama') is-invalid @enderror"
                                    name="nama" id="nama" value="{{ $produk->nama }}">
                                @error('nama')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-2">
                                <label for="deskripsi">Deskripsi</label>
                                <textarea class="form-control @error('deskripsi') is-invalid @enderror" name="deskripsi" id="deskripsi">{{ $produk->deskripsi }}</textarea>
                                @error('deskripsi')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-2">
                                <label for="stok">Stok</label>
                                <input type="number" class="form-control @error('stok') is-invalid @enderror"
                                    name="stok" id="stok" value="{{ $produk->stok }}">
                                @error('stok')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-2">
                                <label for="harga">Harga</label>
                                <input type="number" step="0.01"
                                    class="form-control @error('harga') is-invalid @enderror" name="harga" id="harga"
                                    value="{{ $produk->harga }}">
                                @error('harga')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-2">
                                <label for="kategori_id">Kategori</label>
                                <select class="form-control @error('kategori_id') is-invalid @enderror" name="kategori_id"
                                    id="kategori_id">
                                    @foreach ($kategoris as $kategori)
                                        <option value="{{ $kategori->id }}"
                                            {{ $kategori->id == $produk->kategori_id ? 'selected' : '' }}>
                                            {{ $kategori->nama }}</option>
                                    @endforeach
                                </select>
                                @error('kategori_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-2">
                                <label for="gambar">Gambar</label>
                                <input type="file" class="form-control @error('gambar') is-invalid @enderror"
                                    name="gambar[]" id="gambar" multiple>
                                @error('gambar')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <div class="mt-2">
                                    @if ($produk->gambar && is_array(json_decode($produk->gambar)))
                                        @foreach (json_decode($produk->gambar) as $image)
                                            <img src="{{ asset('storage/' . $image) }}" alt="{{ $produk->nama }}"
                                                width="100" class="img-thumbnail mb-2">
                                        @endforeach
                                    @else
                                        <span>No Image</span>
                                    @endif
                                </div>
                            </div>
                            <div class="mb-2">
                                <label for="ukuran">Ukuran</label>
                                <input type="text" class="form-control @error('ukuran') is-invalid @enderror"
                                    name="ukuran" id="ukuran" value="{{ implode(', ', json_decode($produk->ukuran)) }}"
                                    placeholder="Contoh: S, M, L, XL">
                                @error('ukuran')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-2">
                                <button class="btn btn-sm btn-success" type="submit">
                                    Update
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include CKEditor 5 script -->
    <script src="https://cdn.ckeditor.com/ckeditor5/35.0.1/classic/ckeditor.js"></script>
    <script>
        // Initialize CKEditor 5
        ClassicEditor
            .create(document.querySelector('#deskripsi'))
            .catch(error => {
                console.error(error);
            });
    </script>
@endsection
