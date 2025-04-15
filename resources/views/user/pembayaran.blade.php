@extends('user.layout.layout')

@section('content')
    <!-- Breadcrumb Section -->
    <section class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__text">
                        <h4>Payment</h4>
                        <div class="breadcrumb__links">
                            <a href="{{ route('home') }}">Home</a>
                            <a href="{{ route('keranjang.index') }}">Cart</a>
                            <span>Payment</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Payment Section -->
    <section class="payment-section spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <h5>Selected Products</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Size</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($selectedItems as $item)
                                    <tr>
                                        <td class="d-flex align-items-center">
                                            <div class="product-pic me-3">
                                                @if ($item->produk->gambar && is_array(json_decode($item->produk->gambar, true)))
                                                    <img src="{{ asset('storage/' . json_decode($item->produk->gambar, true)[0]) }}"
                                                         alt="{{ $item->produk->nama }}" width="80" class="img-fluid rounded-3">
                                                @else
                                                    <span>No Image</span>
                                                @endif
                                            </div>
                                            <div>
                                                <h6>{{ $item->produk->nama }}</h6>
                                            </div>
                                        </td>
                                        <td>{{ $item->ukuran }}</td>
                                        <td>{{ $item->jumlah }}</td>
                                        <td>Rp.{{ number_format($item->produk->harga, 0, ',', '.') }}</td>
                                        <td class="text-danger fw-bold">
                                            Rp.{{ number_format($item->produk->harga * $item->jumlah, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="payment-summary p-4 border bg-light">
                        <h5>Summary</h5>
                        <ul class="list-unstyled">
                            <li class="d-flex justify-content-between">
                                <span>Subtotal</span>
                                <span class="text-danger fw-bold">Rp.{{ number_format($subtotal, 0, ',', '.') }}</span>
                            </li>
                            <li class="d-flex justify-content-between">
                                <span>Shipping</span>
                                <span class="text-danger fw-bold">Rp.{{ number_format($shipping, 0, ',', '.') }}</span>
                            </li>
                            <li class="d-flex justify-content-between">
                                <span>Total</span>
                                <span class="text-danger fw-bold">Rp.{{ number_format($total, 0, ',', '.') }}</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Tombol dengan pemesanan ID -->
                    <button type="button"
                            class="btn btn-dark w-100 pay-button"
                            data-id="{{ $pemesananId }}">
                        Confirm Payment
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Midtrans Script -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const payButtons = document.querySelectorAll('.pay-button');

            payButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const pemesananId = this.getAttribute('data-id');

                    Swal.fire({
                        title: 'Memproses Pembayaran',
                        text: 'Mohon tunggu...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch('/api/midtrans/create-transaction', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ pemesanan_id: pemesananId })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.snap_token) {
                            Swal.close();

                            window.snap.pay(data.snap_token, {
                                onSuccess: function (result) {
                                    Swal.fire({
                                        title: 'Success',
                                        text: 'Pembayaran berhasil!',
                                        icon: 'success',
                                        timer: 2000,
                                        timerProgressBar: true,
                                        showConfirmButton: false
                                    }).then(() => location.reload());
                                },
                                onPending: function (result) {
                                    Swal.fire({
                                        title: 'Info',
                                        text: 'Pembayaran sedang diproses',
                                        icon: 'info',
                                        timer: 2000,
                                        timerProgressBar: true,
                                        showConfirmButton: false
                                    }).then(() => location.reload());
                                },
                                onError: function (result) {
                                    Swal.fire({
                                        title: 'Error',
                                        text: 'Pembayaran gagal!',
                                        icon: 'error',
                                        timer: 2000,
                                        timerProgressBar: true,
                                        showConfirmButton: false
                                    });
                                },
                                onClose: function () {
                                    Swal.fire({
                                        title: 'Info',
                                        text: 'Pembayaran dibatalkan',
                                        icon: 'info',
                                        timer: 2000,
                                        timerProgressBar: true,
                                        showConfirmButton: false
                                    });
                                }
                            });
                        } else {
                            throw new Error(data.error || 'Gagal mendapatkan snap token');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error',
                            text: 'Terjadi kesalahan: ' + error.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    });
                });
            });
        });
    </script>
@endsection
