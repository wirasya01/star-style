@extends('user.layout.layout')

@section('content')
<div class="checkout-section spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="checkout-form">
                    <h2>Checkout Pembayaran</h2>
                    <form action="{{ route('pembayaran') }}" method="POST" id="checkout-form">
                        @csrf
                        
                        <!-- Customer Information -->
                        <div class="form-group">
                            <h4>Informasi Pelanggan</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="text" name="nama" placeholder="Nama Lengkap" value="{{ old('nama') }}" required>
                                    @error('nama')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <input type="text" name="no_telp" placeholder="No. Telepon" value="{{ old('no_telp') }}" required>
                            @error('no_telp')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                            <textarea name="alamat" placeholder="Alamat Lengkap" required>{{ old('alamat') }}</textarea>
                            @error('alamat')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

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
                                        <input type="text" name="card_number" placeholder="Nomor Kartu" class="credit-card">
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

                        <button type="submit" class="site-btn">Konfirmasi Pembayaran</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="checkout-cart">
                    <h4>Ringkasan Pesanan</h4>
                    <ul class="product-list">
                        @foreach($selectedItems as $item)
                        <li>
                            <div class="pl-thumb">
                                <img src="{{ asset('storage/'.$item->produk->image) }}" alt="">
                            </div>
                            <div class="pl-content">
                                <h6>{{ $item->produk->nama }}</h6>
                                <p>Rp {{ number_format($item->produk->harga, 0, ',', '.') }} x {{ $item->jumlah }}</p>
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
        for(var i = 0; i < val.length; i++) {
            if(i%4 == 0 && i > 0) newVal += ' ';
            newVal += val[i];
        }
        $(this).val(newVal);
    });
});
</script>
@endsection
