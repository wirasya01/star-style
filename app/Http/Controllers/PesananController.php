<?php
namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Produk;
use Illuminate\Http\Request;

class PesananController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $produk = Produk::findOrFail($request->produk_id);
        return view('user.checkout', compact('produk'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Create new order
        $pesanan = new Pesanan();
        $pesanan->pembeli_id = auth()->id();
        $pesanan->tanggal_pesan = now();
        $pesanan->total_harga = $request->total;
        $pesanan->status = 'Pending';
        $pesanan->metode_pembayaran = $request->payment;
        $pesanan->alamat_pengiriman = $request->alamat;
        $pesanan->save();

        // Add order items from cart
        $cartItems = \App\Models\Keranjang::with('produk')
            ->where('pembeli_id', auth()->id())
            ->get();

        foreach ($cartItems as $item) {
            $detail = new \App\Models\DetailPesanan();
            $detail->pesanan_id = $pesanan->id;
            $detail->produk_id = $item->produk_id;
            $detail->jumlah = $item->jumlah;
            $detail->harga = $item->produk->price;
            $detail->subtotal = $item->produk->price * $item->jumlah;
            $detail->save();

            // Remove item from cart
            $item->delete();
        }

        // Create payment record
        $pembayaran = new \App\Models\Pembayaran();
        $pembayaran->pesanan_id = $pesanan->id;
        $pembayaran->jumlah = $request->total;
        $pembayaran->metode = $request->payment;
        $pembayaran->status = 'Pending';
        $pembayaran->save();

        return redirect()->route('pesanan.show', $pesanan->id)
            ->with('success', 'Pesanan berhasil dibuat!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $pesanan = Pesanan::with('detailPesanans.produk')->findOrFail($id);
        return view('detail_pesanan', compact('pesanan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pesanan $pesanan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pesanan $pesanan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pesanan $pesanan)
    {
        //
    }
}
