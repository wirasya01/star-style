<!-- filepath: /Users/rasyap.s/UJIKOM/star-style/resources/views/admin/produk/index.blade.php -->
@extends('admin.layouts.app')
@section('page-title', 'Produk')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                @if (session('success'))
                    <div class="alert alert-success fade show" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="card">
                    <div class="card-header">Data Produk <br>
                        <a href="{{ route('produk.create') }}" class="btn btn-sm btn-primary float-right">Add</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="table-baru">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Produk</th>
                                        <th>Deskripsi</th>
                                        <th>Stok</th>
                                        <th>Harga</th>
                                        <th>Kategori</th>
                                        <th>Gambar</th>
                                        <th>Ukuran</th> <!-- Tambahkan kolom Ukuran -->
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                @php $no = 1; @endphp
                                <tbody>
                                    @foreach ($produks as $item)
                                        <tr>
                                            <td>{{ $no++ }}</td>
                                            <td>{{ $item->nama }}</td>
                                            <td>{!! $item->deskripsi !!}</td>
                                            <td>{{ $item->stok }}</td>
                                            <td>Rp{{ number_format($item->harga) }}</td>
                                            <td>{{ $item->kategori->nama }}</td>
                                            <td>
                                                @if ($item->gambar && is_array(json_decode($item->gambar)))
                                                    @foreach (json_decode($item->gambar) as $image)
                                                        <img src="{{ asset('storage/' . $image) }}"
                                                            alt="{{ $item->nama }}" width="50"
                                                            class="img-thumbnail mb-2">
                                                    @endforeach
                                                @else
                                                    <span>No Image</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($item->ukuran && is_array(json_decode($item->ukuran)))
                                                    @foreach (json_decode($item->ukuran) as $ukuran)
                                                        <span class="badge badge-info">{{ $ukuran }}</span>
                                                    @endforeach
                                                @else
                                                    <span>No Size</span>
                                                @endif
                                            </td>
                                            <td>
                                                <form action="{{ route('produk.destroy', $item->id) }}" method="post">
                                                    @method('DELETE')
                                                    @csrf
                                                    <a href="{{ route('produk.edit', $item->id) }}"
                                                        class="btn btn-sm btn-success mb-2">Edit</a>
                                                    <a href="{{ route('produk.show', $item->id) }}"
                                                        class="btn btn-sm btn-warning mb-2">Show</a>
                                                    <button class="btn btn-sm btn-danger" type="submit"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
