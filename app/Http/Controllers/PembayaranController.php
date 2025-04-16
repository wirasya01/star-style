<?php
namespace App\Http\Controllers;

use App\Models\Keranjang;
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

    // ✅ ADMIN: Tampilkan semua pembayaran
    public function index()
    {
        $pembayarans = Pembayaran::latest()->with('pesanan')->get();
        return view('admin.pembayaran.index', compact('pembayarans'));
    }

    /**
     * Handle Midtrans payment notification webhook
     */
    public function handleNotification(Request $request)
    {
        $serverKey = config('midtrans.server_key');
        $input = $request->getContent();
        $signatureKey = $request->header('X-Signature-Key');

        // Verify signature
        $expectedSignature = hash('sha512', $input . $serverKey);
        if ($signatureKey !== $expectedSignature) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $notification = json_decode($input, true);

        $orderId = $notification['order_id'] ?? null;
        $transactionStatus = $notification['transaction_status'] ?? null;
        $fraudStatus = $notification['fraud_status'] ?? null;

        if (!$orderId) {
            return response()->json(['message' => 'Order ID not found'], 400);
        }

        // Extract pesanan_id from order_id format "PESANAN-{id}-{timestamp}"
        preg_match('/PESANAN-(\d+)-\d+/', $orderId, $matches);
        $pesananId = $matches[1] ?? null;

        if (!$pesananId) {
            return response()->json(['message' => 'Invalid order ID format'], 400);
        }

        $pembayaran = Pembayaran::where('pesanan_id', $pesananId)->first();
        $pesanan = Pesanan::find($pesananId);

        if (!$pesanan) {
            return response()->json(['message' => 'Pesanan not found'], 404);
        }

        // Determine payment status based on Midtrans transaction status
        $statusPembayaran = 'pending';
        $statusPesanan = 'pending';

        switch ($transactionStatus) {
            case 'capture':
                if ($fraudStatus === 'challenge') {
                    $statusPembayaran = 'challenge';
                    $statusPesanan = 'pending';
                } else if ($fraudStatus === 'accept') {
                    $statusPembayaran = 'success';
                    $statusPesanan = 'paid';
                }
                break;
            case 'settlement':
                $statusPembayaran = 'success';
                $statusPesanan = 'paid';
                break;
            case 'pending':
                $statusPembayaran = 'pending';
                $statusPesanan = 'pending';
                break;
            case 'deny':
            case 'cancel':
            case 'expire':
                $statusPembayaran = 'failed';
                $statusPesanan = 'cancelled';
                break;
            default:
                $statusPembayaran = 'pending';
                $statusPesanan = 'pending';
                break;
        }

        // Update or create Pembayaran record
        if ($pembayaran) {
            $pembayaran->update([
                'status_pembayaran'  => $statusPembayaran,
                'tanggal_pembayaran' => now(),
            ]);
        } else {
            Pembayaran::create([
                'pesanan_id'         => $pesananId,
                'metode_pembayaran'  => 'Midtrans',
                'status_pembayaran'  => $statusPembayaran,
                'tanggal_pembayaran' => now(),
            ]);
        }

        // Update Pesanan status
        $pesanan->update([
            'status' => $statusPesanan,
        ]);

        return response()->json(['message' => 'Notification processed']);
    }

    // ✅ ADMIN: Tampilkan form create pembayaran
    public function create()
    {
        $pesanans = Pesanan::all();
        return view('admin.pembayaran.create', compact('pesanans'));
    }

    // ✅ ADMIN: Simpan data pembayaran baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pesanan_id'         => 'required|exists:pesanans,id',
            'metode_pembayaran'  => 'required|string|max:255',
            'status_pembayaran'  => 'required|string|max:255',
            'tanggal_pembayaran' => 'nullable|date',
        ]);

        Pembayaran::create($validated);

        return redirect()->route('pembayaran.index')
            ->with('success', 'Pembayaran berhasil dibuat.');
    }

    // ✅ ADMIN: Detail pembayaran
    public function show($id)
    {
        $pembayaran = Pembayaran::with('pesanan')->findOrFail($id);
        return view('admin.pembayaran.show', compact('pembayaran'));
    }

    // ✅ ADMIN: Form edit pembayaran
    public function edit($id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        $pesanans   = Pesanan::all();
        return view('admin.pembayaran.edit', compact('pembayaran', 'pesanans'));
    }

    // ✅ ADMIN: Update pembayaran
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'pesanan_id'         => 'required|exists:pesanans,id',
            'metode_pembayaran'  => 'required|string|max:255',
            'status_pembayaran'  => 'required|string|max:255',
            'tanggal_pembayaran' => 'nullable|date',
        ]);

        $pembayaran = Pembayaran::findOrFail($id);
        $pembayaran->update($validated);

        return redirect()->route('pembayaran.index')
            ->with('success', 'Pembayaran berhasil diperbarui.');
    }

    // ✅ ADMIN: Hapus pembayaran
    public function destroy($id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        $pembayaran->delete();

        return redirect()->route('pembayaran.index')
            ->with('success', 'Pembayaran berhasil dihapus.');
    }

    // ✅ USER: Halaman pembayaran (otomatis buat pesanan)
    public function showPaymentPage(Request $request)
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

        // Hitung total dan jumlah
        $subtotal = 0;
        $totalJumlah = 0;
        foreach ($selectedItems as $item) {
            $subtotal += $item->produk->harga * $item->jumlah;
            $totalJumlah += $item->jumlah;
        }
        $shipping = 10000;
        $total    = $subtotal + $shipping;

        // Simpan pesanan
        $pesanan = Pesanan::create([
            'pembeli_id'    => $userId,
            'total_harga'   => $total,
            'jumlah'        => $totalJumlah,
            'status'        => 'pending',
            'tanggal_pesan' => now(),
            'metode_pembayaran'  => 'Midtrans',
        ]);

        // Simpan detail item ke detailPesanans
        foreach ($selectedItems as $item) {
            $pesanan->detailPesanans()->create([
                'produk_id' => $item->produk_id,
                'jumlah'    => $item->jumlah,
                'ukuran'    => !empty($item->ukuran) ? (string)$item->ukuran : 'default',
                'variasi'   => !empty($item->variasi) ? (string)$item->variasi : 'default',
                'harga'     => $item->produk->harga,
            ]);
        }

        return view('user.pembayaran', [
            'selectedItems' => $selectedItems,
            'subtotal'      => $subtotal,
            'shipping'      => $shipping,
            'total'         => $total,
            'pemesananId'   => $pesanan->id, // 👉 dikirim ke view
        ]);
    }

    // ✅ USER: Simpan data pembayaran user
    public function storeUserPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pesanan_id'         => 'required|exists:pesanans,id',
            'metode_pembayaran'  => 'required|string|max:255',
            'status_pembayaran'  => 'required|string|max:255',
            'tanggal_pembayaran' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Pembayaran::create([
            'pesanan_id'         => $request->input('pesanan_id'),
            'metode_pembayaran'  => $request->input('metode_pembayaran'),
            'status_pembayaran'  => $request->input('status_pembayaran'),
            'tanggal_pembayaran' => $request->input('tanggal_pembayaran'),
        ]);

        return redirect()->route('home')->with('success', 'Pembayaran berhasil diproses.');
    }

    // ✅ API: Get semua pembayaran
    public function getPayments()
    {
        $payments = Pembayaran::with('pesanan')->get()->map(function ($payment) {
            return [
                'id'                 => $payment->id,
                'pesanan_id'         => $payment->pesanan_id,
                'metode_pembayaran'  => $payment->metode_pembayaran,
                'status_pembayaran'  => $payment->status_pembayaran,
                'tanggal_pembayaran' => $payment->tanggal_pembayaran,
                'pesanan'            => $payment->pesanan,
            ];
        });

        return response()->json($payments);
    }
}
