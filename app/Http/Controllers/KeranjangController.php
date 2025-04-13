<?php
namespace App\Http\Controllers;

use App\Models\Keranjang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KeranjangController extends Controller
{
    public function index()
    {
        // Mengambil semua item keranjang untuk pengguna yang sedang login
        $keranjang = Keranjang::with('produk')->where('pembeli_id', Auth::id())->get();
        return view('user.keranjang.index', compact('keranjang'));
    }

    public function store(Request $request)
    {
        // Validasi input dari pengguna
        $validated = $request->validate([
            'produk_id' => 'required|exists:produks,id',
            'ukuran'    => 'required|string',
            'jumlah'    => 'required|integer|min:1',
        ]);

        // Mencari apakah produk sudah ada di keranjang
        $keranjang = Keranjang::where('pembeli_id', Auth::id())
            ->where('produk_id', $request->produk_id)
            ->first();

        if ($keranjang) {
            // Jika produk sudah ada, tambahkan jumlahnya
            $keranjang->jumlah += $request->jumlah;
            $keranjang->save();
        } else {
            // Jika produk belum ada, buat entri baru di keranjang
            Keranjang::create([
                'pembeli_id' => Auth::id(), // Menambahkan pembeli_id
                'produk_id'  => $request->produk_id,
                'ukuran'     => $request->ukuran, // Menyimpan ukuran yang dipilih
                'jumlah'     => $request->jumlah,
            ]);

        }

        return redirect()->back()->with('success', 'Product added to cart successfully!');
    }

    public function destroy($id)
    {
        // Menghapus item dari keranjang berdasarkan ID
        $keranjang = Keranjang::where('pembeli_id', Auth::id())->where('id', $id)->first();

        if ($keranjang) {
            $keranjang->delete();
            return redirect()->back()->with('success', 'Product removed from cart successfully!');
        }

        return redirect()->back()->with('error', 'Product not found in cart.');
    }

    public function show($id)
    {
        $keranjang = Keranjang::findOrFail($id);
        return view('user.keranjang.show', compact('keranjang'));
    }

    public function edit($id)
    {
        $keranjang = Keranjang::findOrFail($id);
        return view('user.keranjang.edit', compact('keranjang'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'ukuran' => 'required|string',
            'jumlah' => 'required|integer|min:1',
        ]);

        $keranjang = Keranjang::findOrFail($id);
        $keranjang->update($validated);

        return redirect()->route('keranjang.index')
            ->with('success', 'Item keranjang berhasil diperbarui');
    }
}
