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
        $request->validate([
            'total'   => 'required|numeric',
            'payment' => 'required|string',
            'alamat'  => 'required|string',
        ]);

        $cartItems = Keranjang::with('produk')
            ->where('pembeli_id', auth()->id())
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('keranjang.index')->with('error', 'Keranjang Anda kosong.');
        }

        $jumlahTotalItem = $cartItems->sum('jumlah');

        // Simpan pesanan
        $pesanan = Pesanan::create([
            'pembeli_id'        => auth()->id(),
            'tanggal_pesan'     => now(),
            'total_harga'       => $request->total,
            'jumlah'            => $jumlahTotalItem,
            'metode_pembayaran' => $request->payment,
            'status'            => 'Pending',
        ]);

        // Simpan detail pesanan
        foreach ($cartItems as $item) {
            DetailPesanan::create([
                'pesanan_id' => $pesanan->id,
                'produk_id'  => $item->produk_id,
                'jumlah'     => $item->jumlah,
                'harga'      => $item->produk->price,
                'subtotal'   => $item->produk->price * $item->jumlah,
            ]);

            $item->delete(); // hapus dari keranjang
        }

        // Simpan pembayaran
        Pembayaran::create([
            'pesanan_id'         => $pesanan->id,
            'metode_pembayaran'  => $request->payment,
            'status_pembayaran'  => 'Pending',
            'tanggal_pembayaran' => now(),
        ]);

        return redirect()->route('pesanan.show', $pesanan->id)
            ->with('success', 'Pesanan berhasil dibuat!');
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
