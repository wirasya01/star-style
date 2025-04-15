@extends('user.layout.layout')

@section('content')
    <!-- Breadcrumb Section -->
    <section class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__text">
                        <h4>Shopping Cart</h4>
                        <div class="breadcrumb__links">
                            <a href="{{ route('home') }}">Home</a>
                            <a href="{{ route('shop') }}">Shop</a>
                            <span>Shopping Cart</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Shopping Cart Section -->
    <section class="shopping-cart spad">
        <div class="container">
            <form id="cartForm" action="{{ route('pembayaran.show') }}" method="GET">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <div class="shopping__cart__table table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectAll"></th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($keranjang as $item)
                                    <tr>
                                        <td class="align-middle">
                                            <input type="checkbox" class="product-checkbox" name="selected_products[]"
                                                value="{{ $item->id }}"
                                                data-price="{{ $item->produk->harga * $item->jumlah }}">
                                        </td>
                                        <td class="product__cart__item d-flex align-items-center">
                                            <div class="product__cart__item__pic">
                                                @if ($item->produk->gambar && is_array(json_decode($item->produk->gambar, true)))
                                                    <img src="{{ asset('storage/' . json_decode($item->produk->gambar, true)[0]) }}"
                                                        class="img-fluid rounded-3" alt="{{ $item->produk->nama }}"
                                                        width="80">
                                                @else
                                                    <span>No Image</span>
                                                @endif
                                            </div>
                                            <div class="product__cart__item__text ms-3">
                                                <h6 class="mb-1">
                                                    <a href="{{ route('keranjang.show', $item->id) }}" class="text-dark">
                                                        {{ $item->produk->nama }}
                                                    </a>
                                                </h6>
                                                <h5 class="text-danger">
                                                    Rp.{{ number_format($item->produk->harga, 0, ',', '.') }}</h5>
                                                <p class="text-muted mb-0">Size: {{ $item->ukuran }}</p>
                                                <!-- Menampilkan ukuran -->
                                            </div>
                                        </td>
                                        <td class="quantity__item">
                                            <div class="input-group quantity">
                                                <h4 class="text-danger">{{ $item->jumlah }}</h4>
                                            </div>
                                        </td>
                                        <td class="cart__price text-danger fw-bold">
                                            Rp.{{ number_format($item->produk->harga * $item->jumlah, 0, ',', '.') }}
                                        </td>
                                        <td class="cart__close">
                                            <form action="{{ route('keranjang.destroy', $item->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link text-danger"><i
                                                        class="fas fa-times"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('shop') }}" class="btn btn-outline-dark">Continue Shopping</a>
                        <a href="#" class="btn btn-dark"><i class="fa fa-spinner"></i> Update Cart</a>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="cart__discount p-3 border">
                        <h6>Discount Codes</h6>
                        <form action="#">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Coupon code">
                                <button type="submit" class="btn btn-dark">Apply</button>
                            </div>
                        </form>
                    </div>

                    <div class="cart__total p-4 mt-4 border bg-light">
                        <h6>Cart Total</h6>
                        <ul class="list-unstyled">
                            <li class="d-flex justify-content-between">
                                <span>Subtotal</span>
                                <span class="text-danger fw-bold" id="subtotal">Rp.0</span>
                            </li>
                            <li class="d-flex justify-content-between">
                                <span>Total</span>
                                <span class="text-danger fw-bold" id="total">Rp.0</span>
                            </li>
                        </ul>
                        <button type="submit" class="btn btn-dark w-100 mt-3" id="checkoutBtn" disabled>
                            Proceed to Checkout
                        </button>
                    </div>
                </div>
            </div>
            </form>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.product-checkbox');
    const selectAll = document.getElementById('selectAll');
    const subtotalEl = document.getElementById('subtotal');
    const totalEl = document.getElementById('total');
    const checkoutBtn = document.getElementById('checkoutBtn');

    function updateTotal() {
        let subtotal = 0;

        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                // Get price directly from the data-price attribute we set on the checkbox
                const price = parseFloat(checkbox.dataset.price);
                subtotal += price;
            }
        });

        // Format numbers with Indonesian number format
        subtotalEl.innerText = 'Rp.' + subtotal.toLocaleString('id-ID');
        totalEl.innerText = 'Rp.' + subtotal.toLocaleString('id-ID'); // No additional fee
        
        // Enable or disable checkout button based on selection
        checkoutBtn.disabled = subtotal === 0;
    }

    // Event listener for product checkboxes
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateTotal);
    });

    // Event listener for "Select All" checkbox
    selectAll.addEventListener('change', function() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateTotal();
    });

    // Initialize total when page loads
    updateTotal();
});
    </script>
@endsection
