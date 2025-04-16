<?php
namespace App\Http\Controllers;

use App\Models\Pesanan;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    public function __construct()
    {
        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized  = config('midtrans.is_sanitized');
        Config::$is3ds        = config('midtrans.is_3ds');
    }

    public function checkoutPage($id)
    {
        $pesanan = Pesanan::with('pembeli')->findOrFail($id);
        return view('user.checkout', compact('pesanan'));
    }

    public function createTransaction(Request $request)
    {
        $pemesananId = $request->input('pemesanan_id');

        $pesanan = Pesanan::with('pembeli')->find($pemesananId);

        if (!$pesanan) {
            return response()->json(['error' => 'Pesanan tidak ditemukan'], 404);
        }

        $params = [
            'transaction_details' => [
                'order_id'     => 'PESANAN-' . $pesanan->id . '-' . time(),
                'gross_amount' => (int) $pesanan->total_harga,
            ],
            'customer_details'    => [
                'first_name' => $pesanan->pembeli->name ?? 'Pembeli',
                'email'      => $pesanan->pembeli->email ?? 'pembeli@example.com',
                'phone'      => $pesanan->pembeli->phone ?? '081234567890',
            ],
        ];

        Log::info('Midtrans transaction params:', $params);

        $snapToken = Snap::getSnapToken($params);

        return response()->json([
            'snap_token' => $snapToken,
            'order_id'   => 'PESANAN-' . $pesanan->id,
        ]);
    }
}
