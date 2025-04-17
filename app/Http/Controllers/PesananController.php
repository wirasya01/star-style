<?php
namespace App\Http\Controllers;

use App\Models\DetailPesanan;
use App\Models\Keranjang;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\Produk;
use Illuminate\Http\Request;

class PesananController extends Controller
{
    public function index()
    {
        // Menampilkan daftar pesanan pembeli yang sedang login
        $pesanans = Pesanan::where('pembeli_id', auth()->id())
            ->with('detailPesanans.produk')
            ->latest()
            ->get();

        return view('user.pesanan.index', compact('pesanans'));
    }

    public function create(Request $request)
    {
        $produk = Produk::findOrFail($request->produk_id);
        return view('user.checkout', compact('produk'));
    }

public function store(Request $request)
{
    $validated = $request->validate([
        'selected_products' => 'required|array',
        'selected_products.*' => 'integer|exists:keranjangs,id',
    ]);

    $userId = Auth::id();
    $selectedItems = Keranjang::with('produk')
        ->whereIn('id', $validated['selected_products'])
        ->where('pembeli_id', $userId)
        ->get();

    // Create a new order
    $pesanan = Pesanan::create([
        'pembeli_id' => $userId,
        'total_harga' => $selectedItems->sum(function($item) {
            return $item->produk->harga * $item->jumlah;
        }),
        'status' => 'pending',
        'tanggal_pesan' => now(),
    ]);

    // Save order details
    foreach ($selectedItems as $item) {
        DetailPesanan::create([
            'pesanan_id' => $pesanan->id,
            'produk_id'  => $item->produk_id,
            'jumlah'     => $item->jumlah,
            'harga'      => $item->produk->harga,
            'ukuran'     => $item->ukuran,
        ]);
    }

    return redirect()->route('pembayaran.show', ['selected_products' => $validated['selected_products']])
        ->with('success', 'Order created successfully!');
}

    public function show($id)
    {
        $pesanan = Pesanan::with('detailPesanans.produk', 'pembayaran')->findOrFail($id);
        return view('user.pesanan.show', compact('pesanan'));
    }

    public function edit(Pesanan $pesanan)
    {
        //
    }

    public function update(Request $request, Pesanan $pesanan)
    {
        //
    }

    public function destroy(Pesanan $pesanan)
    {
        //
    }
}
