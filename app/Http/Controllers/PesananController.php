<?php
namespace App\Http\Controllers;

use App\Models\DetailPesanan;
use App\Models\Keranjang;
use App\Models\Pesanan;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PesananController extends Controller
{
    /**
     * Menampilkan daftar pesanan pembeli yang sedang login.
     */
    public function index()
    {
        // Ambil semua pesanan milik user yang sedang login
        $pesanans = Pesanan::where('pembeli_id', auth()->id())
            ->with(['detailPesanans.produk', 'pembayaran']) // Relasi dengan detail pesanan dan pembayaran
            ->latest()
            ->get();

        return view('user.pesanan.index', compact('pesanans'));
    }

    /**
     * Menampilkan halaman checkout untuk produk tertentu.
     */
    public function create(Request $request)
    {
        $produk = Produk::findOrFail($request->produk_id);
        return view('user.checkout', compact('produk'));
    }

    /**
     * Menyimpan pesanan baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'selected_products'   => 'required|array',
            'selected_products.*' => 'integer|exists:keranjangs,id',
        ]);

        $userId        = Auth::id();
        $selectedItems = Keranjang::with('produk')
            ->whereIn('id', $validated['selected_products'])
            ->where('pembeli_id', $userId)
            ->get();

        // Buat pesanan baru
        $pesanan = Pesanan::create([
            'pembeli_id'    => $userId,
            'total_harga'   => $selectedItems->sum(function ($item) {
                return $item->produk->harga * $item->jumlah;
            }),
            'status'        => 'pending', // Status default
            'tanggal_pesan' => now(),
        ]);

        // Simpan detail pesanan
        foreach ($selectedItems as $item) {
            DetailPesanan::create([
                'pesanan_id' => $pesanan->id,
                'produk_id'  => $item->produk_id,
                'jumlah'     => $item->jumlah,
                'harga'      => $item->produk->harga,
                'ukuran'     => $item->ukuran,
            ]);
        }

        // Redirect ke halaman pembayaran
        return redirect()->route('pembayaran.show', ['pesanan_id' => $pesanan->id])
            ->with('success', 'Pesanan berhasil dibuat!');
    }

    /**
     * Menampilkan detail pesanan tertentu.
     */
    public function show($id)
    {
        $pesanan = Pesanan::with(['detailPesanans.produk', 'pembayaran'])->findOrFail($id);

        return view('user.pesanan.show', compact('pesanan'));
    }

    /**
     * Mengedit pesanan (jika diperlukan).
     */
    public function edit(Pesanan $pesanan)
    {
        // Logika untuk mengedit pesanan
    }

    /**
     * Memperbarui pesanan (jika diperlukan).
     */
    public function update(Request $request, Pesanan $pesanan)
    {
        // Logika untuk memperbarui pesanan
    }

    /**
     * Menghapus pesanan.
     */
    public function destroy(Pesanan $pesanan)
    {
        $pesanan->delete();

        return redirect()->route('pesanan.index')->with('success', 'Pesanan berhasil dihapus.');
    }
}
