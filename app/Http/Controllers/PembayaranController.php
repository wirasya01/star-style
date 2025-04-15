<?php

namespace App\Http\Controllers;

use App\Models\Keranjang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Pembayaran;
use App\Models\Pesanan;
use Illuminate\Support\Facades\Validator;

class PembayaranController extends Controller
{
    public function show(Request $request)
    {
        $selectedProductIds = $request->input('selected_products', []);

        if (empty($selectedProductIds)) {
            return redirect()->route('keranjang.index')->with('error', 'No products selected for payment.');
        }

        $userId = Auth::id();

        $selectedItems = Keranjang::with('produk')
            ->whereIn('id', $selectedProductIds)
            ->where('pembeli_id', $userId)
            ->get();

        if ($selectedItems->isEmpty()) {
            return redirect()->route('keranjang.index')->with('error', 'No valid products selected.');
        }

        $subtotal = 0;
        foreach ($selectedItems as $item) {
            $subtotal += $item->produk->harga * $item->jumlah;
        }
        $shipping = 10000; // fixed shipping cost or calculate dynamically
        $total = $subtotal + $shipping;

        return view('user.pembayaran', [
            'selectedItems' => $selectedItems,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $total,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'no_telp' => 'required|string|max:20',
            'alamat' => 'required|string|max:500',
            'payment' => 'required|string|in:credit,bank,cod',
            // Additional validation for credit card fields if payment is credit
            'card_number' => 'required_if:payment,credit|nullable|string',
            'card_name' => 'required_if:payment,credit|nullable|string',
            'expiry' => 'required_if:payment,credit|nullable|string',
            'cvv' => 'required_if:payment,credit|nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // For simplicity, assume a Pesanan is created here or retrieved from session/cart
        // This example assumes a Pesanan is created with status 'pending'
        $pesanan = Pesanan::create([
            'user_id' => Auth::id(),
            'status' => 'pending',
            'total' => 0, // You may calculate total from cart or request
        ]);

        // Create Pembayaran record
        Pembayaran::create([
            'pesanan_id' => $pesanan->id,
            'metode_pembayaran' => $request->input('payment'),
            'status_pembayaran' => 'pending',
            'tanggal_pembayaran' => now(),
        ]);

        // Redirect to a confirmation page or order summary
        return redirect()->route('home')->with('success', 'Pembayaran berhasil diproses.');
    }
}
