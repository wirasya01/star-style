<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PembayaranController extends Controller
{
    public $fillable = [
        'pesanan_id', 'metode_pembayaran', 'status_pembayaran', 'tanggal_pembayaran',
    ];

    public $visible = [
        'pesanan_id', 'metode_pembayaran', 'status_pembayaran', 'tanggal_pembayaran',
    ];

    /**
     * Display a listing of the resource for admin.
     */
    public function index()
    {
        $pembayarans = Pembayaran::latest()->with('pesanan')->get();
        return view('admin.pembayaran.index', compact('pembayarans'));
    }

    /**
     * Show the form for creating a new resource for admin.
     */
    public function create()
    {
        $pesanans = Pesanan::all();
        return view('admin.pembayaran.create', compact('pesanans'));
    }

    /**
     * Store a newly created resource in storage for admin.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pesanan_id' => 'required|exists:pesanans,id',
            'metode_pembayaran' => 'required|string|max:255',
            'status_pembayaran' => 'required|string|max:255',
            'tanggal_pembayaran' => 'nullable|date',
        ]);

        Pembayaran::create($validated);

        return redirect()->route('pembayaran.index')
            ->with('success', 'Pembayaran berhasil dibuat.');
    }

    /**
     * Display the specified resource for admin.
     */
    public function show($id)
    {
        $pembayaran = Pembayaran::with('pesanan')->findOrFail($id);
        return view('admin.pembayaran.show', compact('pembayaran'));
    }

    /**
     * Show the form for editing the specified resource for admin.
     */
    public function edit($id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        $pesanans = Pesanan::all();
        return view('admin.pembayaran.edit', compact('pembayaran', 'pesanans'));
    }

    /**
     * Update the specified resource in storage for admin.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'pesanan_id' => 'required|exists:pesanans,id',
            'metode_pembayaran' => 'required|string|max:255',
            'status_pembayaran' => 'required|string|max:255',
            'tanggal_pembayaran' => 'nullable|date',
        ]);

        $pembayaran = Pembayaran::findOrFail($id);
        $pembayaran->update($validated);

        return redirect()->route('pembayaran.index')
            ->with('success', 'Pembayaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage for admin.
     */
    public function destroy($id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        $pembayaran->delete();

        return redirect()->route('pembayaran.index')
            ->with('success', 'Pembayaran berhasil dihapus.');
    }

    /**
     * Display the payment page for user with selected products.
     */
    public function showPaymentPage(Request $request)
    {
        $selectedProductIds = $request->input('selected_products', []);

        if (empty($selectedProductIds)) {
            return redirect()->route('keranjang.index')->with('error', 'No products selected for payment.');
        }

        $userId = Auth::id();

        $selectedItems = \App\Models\Keranjang::with('produk')
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

    /**
     * Store payment from user.
     */
    public function storeUserPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pesanan_id' => 'required|exists:pesanans,id',
            'metode_pembayaran' => 'required|string|max:255',
            'status_pembayaran' => 'required|string|max:255',
            'tanggal_pembayaran' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Pembayaran::create([
            'pesanan_id' => $request->input('pesanan_id'),
            'metode_pembayaran' => $request->input('metode_pembayaran'),
            'status_pembayaran' => $request->input('status_pembayaran'),
            'tanggal_pembayaran' => $request->input('tanggal_pembayaran'),
        ]);

        return redirect()->route('home')->with('success', 'Pembayaran berhasil diproses.');
    }

    /**
     * Get all payments as JSON for API or other uses.
     */
    public function getPayments()
    {
        $payments = Pembayaran::with('pesanan')->get()->map(function ($payment) {
            return [
                'id' => $payment->id,
                'pesanan_id' => $payment->pesanan_id,
                'metode_pembayaran' => $payment->metode_pembayaran,
                'status_pembayaran' => $payment->status_pembayaran,
                'tanggal_pembayaran' => $payment->tanggal_pembayaran,
                'pesanan' => $payment->pesanan,
            ];
        });

        return response()->json($payments);
    }
}
