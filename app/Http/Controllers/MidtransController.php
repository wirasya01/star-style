<?php
namespace App\Http\Controllers;

use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransController extends Controller
{
    public function callback(Request $request)
    {
        $serverKey = config('midtrans.server_key');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed === $request->signature_key) {
            // Verification successful, update order status based on order_id
            $orderId = $request->order_id;
            $transactionStatus = $request->transaction_status;

            // Update payment data in the database according to $orderId and $transactionStatus
            $pesanan = Pesanan::where('id', $orderId)->first();
            if ($pesanan) {
                $pesanan->status = $transactionStatus; // Update the status accordingly
                $pesanan->save();
            }
        }

        return response()->json(['message' => 'Callback received']);
    }
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

        if (! $pesanan || ! $pesanan->total_harga) {
            return response()->json(['error' => 'Pesanan tidak valid.'], 404);
        }

        try {
            $orderId = 'PESANAN-' . $pesanan->id;

            $params = [
                'transaction_details' => [
                    'order_id'     => $orderId,
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
                'order_id'   => $orderId,
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans Snap Error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal membuat transaksi.'], 500);
        }
    }
}
