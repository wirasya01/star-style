@extends('user.layout.layout')

@section('content')
    <!-- Breadcrumb -->
    <section class="breadcrumb-option py-3 bg-light" data-aos="fade-down">
        <div class="container">
            <h4 class="fw-bold">Payment</h4>
            <div class="breadcrumb__links">
                <a href="{{ route('home') }}" class="text-decoration-none text-dark">Home</a>
                <a href="{{ route('keranjang.index') }}" class="text-decoration-none text-dark">Cart</a>
                <span class="text-muted">Payment</span>
            </div>
        </div>
    </section>

    <!-- Payment Section -->
    <section class="payment-section spad py-5">
        <div class="container">
            <div class="row g-4">
                <!-- Left: Produk -->
                <div class="col-lg-8" data-aos="fade-up">
                    <div class="card shadow-lg border-0">
                        <div class="card-body">
                            <h5 class="fw-bold mb-4">🛍️ Selected Products</h5>
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Product</th>
                                            <th>Size</th>
                                            <th>Qty</th>
                                            <th>Price</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($selectedItems as $item)
                                            <tr>
                                                <td class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        @if ($item->produk->gambar && is_array(json_decode($item->produk->gambar, true)))
                                                            <img src="{{ asset('storage/' . json_decode($item->produk->gambar, true)[0]) }}"
                                                                alt="{{ $item->produk->nama }}" width="70"
                                                                class="img-thumbnail rounded-3 shadow-sm">
                                                        @else
                                                            <span>No Image</span>
                                                        @endif
                                                    </div>
                                                    <div><strong>{{ $item->produk->nama }}</strong></div>
                                                </td>
                                                <td>{{ $item->ukuran }}</td>
                                                <td>{{ $item->jumlah }}</td>
                                                <td>Rp{{ number_format($item->produk->harga, 0, ',', '.') }}</td>
                                                <td class="text-danger fw-bold">
                                                    Rp{{ number_format($item->produk->harga * $item->jumlah, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Ringkasan -->
                <div class="col-lg-4" data-aos="fade-left">
                    <div class="card shadow-lg border-0 bg-white">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4">🧾 Order Summary</h5>
                            <ul class="list-group mb-4">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Subtotal
                                    <span class="text-danger fw-semibold">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Shipping
                                    <span class="text-danger fw-semibold">Rp{{ number_format($shipping, 0, ',', '.') }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center fw-bold fs-5">
                                    Total
                                    <span class="text-danger">Rp{{ number_format($total, 0, ',', '.') }}</span>
                                </li>
                            </ul>

                            <!-- Confirm Button -->
                            <form action="{{ route('pembayaran.store') }}" method="POST" id="payment-form">
                                @csrf
                                <input type="hidden" name="pesanan_id" value="{{ $pemesananId }}">
                                <input type="hidden" name="metode_pembayaran" value="credit_card"> <!-- Example value -->
                                <input type="hidden" name="status_pembayaran" value="pending"> <!-- Example value -->
                                <input type="hidden" name="tanggal_pembayaran" value="{{ now()->format('Y-m-d H:i:s') }}">
                                @csrf
                                <button type="button"
                                class="btn btn-dark btn-lg w-100 rounded-pill shadow-sm pay-button animate__animated animate__pulse"
                                data-id="{{ $pemesananId }}">
                                <i class="bi bi-credit-card me-2"></i>Confirm Payment
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Midtrans Snap.js -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Midtrans Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const payButton = document.querySelector('.pay-button');

            payButton.addEventListener('click', function () {
                const pemesananId = this.getAttribute('data-id');

                Swal.fire({
                    title: 'Memproses Pembayaran',
                    text: 'Mohon tunggu...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                fetch('/user/api/midtrans/create-transaction', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ pemesanan_id: pemesananId })
                })
                .then(response => {
                    if (!response.ok) throw new Error('Gagal terhubung ke server.');
                    return response.json();
                })
                .then(data => {
                    Swal.close();
                    console.log("Snap Token:", data.snap_token); // Debug

                    if (data.snap_token) {
                        window.snap.pay(data.snap_token, {
                            onSuccess: function () {
                                Swal.fire('Success', 'Pembayaran berhasil!', 'success');
                            },
                            onPending: function () {
                                Swal.fire('Pending', 'Pembayaran sedang diproses.', 'info').then(() => location.reload());
                            },
                            onError: function () {
                                Swal.fire('Error', 'Pembayaran gagal!', 'error');
                            },
                            onClose: function () {
                                Swal.fire('Dibatalkan', 'Anda membatalkan pembayaran.', 'info');
                            }
                        });
                    } else {
                        throw new Error('Token pembayaran tidak ditemukan.');
                    }
                })
                .catch(error => {
                    console.error(error);
                    Swal.fire('Error', error.message, 'error');
                });
            });
        });
    </script>

    <!-- JavaScript for form submission -->
    <script>
        document.querySelector('.pay-button').addEventListener('click', function() {
            fetch('/user/pembayaran', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    pesanan_id: {{ $pemesananId }},
                    metode_pembayaran: 'Midtrans', // Example value
                    status_pembayaran: 'pending', // Example value
                    tanggal_pembayaran: '{{ now()->format('Y-m-d H:i:s') }}'
                })
            })
            .then(response => {
                if (!response.ok) throw new Error('Gagal terhubung ke server.');
                return response.json();
            })
            .then(data => {
                console.log(data);
            })
            .catch(error => {
                console.error(error);
            });
        });
    </script>

    <!-- AOS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });
    </script>
@endsection