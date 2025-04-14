<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Keranjang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PembayaranController extends Controller
{
    public function index()
    {
        // Get selected items from session if coming from checkout
        $selectedItems = session('selected_items', []);
        
        $cartItems = Keranjang::with('produk')
            ->where('pembeli_id', Auth::id())
            ->when(!empty($selectedItems), function($query) use ($selectedItems) {
                return $query->whereIn('id', $selectedItems);
            })
            ->get();
            
        $subtotal = $cartItems->sum(function($item) {
            return $item->produk->price * $item->jumlah;
        });
        
        $shipping = 10000; // Flat shipping rate
        $total = $subtotal + $shipping;

        return view('user.pembayaran', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $total
        ]);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'pesanan_id' => 'required|exists:pesanans,id',
            'metode_pembayaran' => 'required|string',
            'status_pembayaran' => 'required|string',
            'tanggal_pembayaran' => 'nullable|date',
            'alamat' => 'required|string',
            'no_telp' => 'required|string',
        ]);

        Pembayaran::create($request->all());
        return redirect()->route('checkout.success');
    }

    public function show(Pembayaran $pembayaran)
    {
        //
    }

    public function edit(Pembayaran $pembayaran)
    {
        //
    }

    public function update(Request $request, Pembayaran $pembayaran)
    {
        $request->validate([
            'pesanan_id' => 'required|exists:pesanans,id',
            'metode_pembayaran' => 'required|string',
            'status_pembayaran' => 'required|string',
            'tanggal_pembayaran' => 'nullable|date',
            'alamat' => 'required|string',
            'no_telp' => 'required|string',
        ]);

        $pembayaran->update($request->all());
        return redirect()->route('checkout.success');
    }

    public function destroy(Pembayaran $pembayaran)
    {
        //
    }
}
