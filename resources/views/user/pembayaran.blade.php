@extends('user.layout.layout')

@section('content')
    <div class="checkout-section spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="checkout-form">
                        <h2>Checkout Pembayaran</h2>
                        <form action="{{ route('createTransaction') }}" method="POST" id="checkout-form">
                            @csrf
                            <!-- Payment Method -->
                            <div class="form-group">
                                <h4>Metode Pembayaran</h4>
                                <div class="payment-method">
                                    <div class="pm-item">
                                        <input type="radio" name="payment" id="credit" value="credit" checked>
                                        <label for="credit">Kartu Kredit</label>
                                    </div>
                                    <div class="pm-item">
                                        <input type="radio" name="payment" id="bank" value="bank">
                                        <label for="bank">Transfer Bank</label>
                                    </div>
                                    <div class="pm-item">
                                        <input type="radio" name="payment" id="cod" value="cod">
                                        <label for="cod">COD (Bayar di Tempat)</label>
                                    </div>
                                </div>

                                <!-- Credit Card Details (shown when credit card selected) -->
                                <div id="credit-card-form" class="payment-details">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="text" name="card_number" placeholder="Nomor Kartu"
                                                class="credit-card">
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" name="card_name" placeholder="Nama di Kartu">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <input type="text" name="expiry" placeholder="MM/YY">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" name="cvv" placeholder="CVV">
                                        </div>
                                    </div>
                                </div>

                                <!-- Bank Transfer Details (shown when bank transfer selected) -->
                                <div id="bank-transfer-form" class="payment-details" style="display:none;">
                                    <p>Silahkan transfer ke rekening berikut:</p>
                                    <p><strong>Bank BCA</strong><br>
                                        123-456-7890<br>
                                        a.n. Toko Fashion</p>
                                </div>
                            </div>

                            <button type="submit" id="pay-button" class="site-btn">Konfirmasi Pembayaran</button>
                        </form>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="checkout-cart">
                        <h4>Ringkasan Pesanan</h4>
                        <ul class="product-list">
                            @foreach ($cartItems as $item)
                                <li>
                                    <div class="pl-thumb">
                                        @if ($item->produk && $item->produk->gambar)
                                            <img src="{{ asset('storage/' . $item->produk->gambar[0]) }}" alt="">
                                        @else
                                            <img src="{{ asset('assets/img/no-image.png') }}" alt="No image">
                                        @endif
                                    </div>
                                    <div class="pl-content">
                                        <h6>{{ $item->produk->nama ?? 'Produk tidak tersedia' }}</h6>
                                        <p>Rp {{ number_format($item->produk->harga ?? 0, 0, ',', '.') }} x
                                            {{ $item->jumlah }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        <ul class="price-list">
                            <li>Subtotal<span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span></li>
                            <li>Ongkir<span>Rp {{ number_format($shipping, 0, ',', '.') }}</span></li>
                            <li class="total">Total<span>Rp {{ number_format($total, 0, ',', '.') }}</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    <script>
        $(document).ready(function() {
            // Show/hide payment forms based on selection
            $('input[name="payment"]').change(function() {
                $('.payment-details').hide();
                $('#' + $(this).val() + '-form').show();
            });

            // Format credit card number
            $('.credit-card').on('input', function() {
                var val = $(this).val().replace(/\s+/g, '');
                var newVal = '';
                for (var i = 0; i < val.length; i++) {
                    if (i % 4 == 0 && i > 0) newVal += ' ';
                    newVal += val[i];
                }
                $(this).val(newVal);
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const payButtons = document.querySelectorAll('.pay-button');
            payButtons.forEach(button => {
                button.addEventListener('click', function() {
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
                            body: JSON.stringify({
                                pemesanan_id: pemesananId
                            })
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
                                    onSuccess: function(result) {
                                        Swal.fire({
                                            title: 'Success',
                                            text: 'Pembayaran berhasil!',
                                            icon: 'success',
                                            timer: 2000,
                                            timerProgressBar: true,
                                            showConfirmButton: false
                                        }).then(() => location.reload());
                                    },
                                    onPending: function(result) {
                                        Swal.fire({
                                            title: 'Info',
                                            text: 'Pembayaran sedang diproses',
                                            icon: 'info',
                                            timer: 2000,
                                            timerProgressBar: true,
                                            showConfirmButton: false
                                        }).then(() => location.reload());
                                    },
                                    onError: function(result) {
                                        Swal.fire({
                                            title: 'Error',
                                            text: 'Pembayaran gagal!',
                                            icon: 'error',
                                            timer: 2000,
                                            timerProgressBar: true,
                                            showConfirmButton: false
                                        });
                                    },
                                    onClose: function() {
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
                                throw new Error(data.error || 'Failed to get payment token');
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
