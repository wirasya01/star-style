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
        $keranjang = Keranjang::findOrFail($id);

        $keranjang->delete();

        return redirect()->route('keranjang.index')->with('success', 'Product deleted successfully!');

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

    public function checkoutSelected(Request $request)
    {
        $request->validate([
            'selected_products'   => 'required|array',
            'selected_products.*' => 'integer|exists:keranjangs,id',
        ]);

        $userId = Auth::id();

        // Fetch selected cart items for the authenticated user
        $selectedItems = Keranjang::with('produk')
            ->whereIn('id', $request->selected_products)
            ->where('pembeli_id', $userId)
            ->get();

        if ($selectedItems->isEmpty()) {
            return redirect()->route('keranjang.index')->with('error', 'No valid products selected.');
        }

        // Pass selected items to the payment page view
        return view('user.pembayaran', ['selectedItems' => $selectedItems]);
    }
}
