<?php
namespace App\Http\Controllers;

class MidtransController extends Controller
{
    public function __construct()
    {
        // Konfigurasi Midtrans
        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized  = config('midtrans.is_sanitized');
        Config::$is3ds        = config('midtrans.is_3ds');

    }

    public function createTransaction($id)
    {
        // Ambil data pesanan
        $pesanan = Pesanan::with('pembeli')->findOrFail($id);

        // Buat parameter Snap
        $params = [
            'transaction_details' => [
                'order_id'     => 'PESANAN-' . $pesanan->id,
                'gross_amount' => $pesanan->total_harga,
            ],
            'customer_details'    => [
                'first_name' => $pesanan->pembeli->name ?? 'Pembeli',
                'email'      => $pesanan->pembeli->email ?? 'pembeli@example.com',
                'phone'      => $pesanan->pembeli->phone ?? '081234567890',
            ],
        ];

        // Ambil Snap Token
        $snapToken = Snap::getSnapToken($params);

        return response()->json([
            'snap_token' => $snapToken,
            'order_id'   => 'PESANAN-' . $pesanan->id,
        ]);
    }
}
