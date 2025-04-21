<?php
namespace App\Http\Controllers;

use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;

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

    public function callback(Request $request)
    {
        Log::info('Midtrans callback received:', $request->all());

        $serverKey = config('midtrans.server_key');
        $input     = $request->all();

        // Validasi Signature
        $signature = hash("sha512", $input['order_id'] . $input['status_code'] . $input['gross_amount'] . $serverKey);

        if ($signature !== $input['signature_key']) {
            Log::warning('Midtrans callback signature mismatch', $input);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $fullOrderId = $input['order_id']; // PESANAN-123
        $realId      = str_replace('PESANAN-', '', $fullOrderId);

        $transactionStatus = $input['transaction_status'];
        $fraudStatus       = $input['fraud_status'] ?? null;

        Log::info('Midtrans callback transaction status:', [
            'order_id' => $realId,
            'transaction_status' => $transactionStatus,
            'fraud_status' => $fraudStatus,
        ]);

        $pesanan = Pesanan::where('id', $realId)->first();

        if (! $pesanan) {
            Log::warning('Midtrans callback order not found', ['order_id' => $realId]);
            return response()->json(['message' => 'Order not found'], 404);
        }

        switch ($transactionStatus) {
            case 'capture':
                if ($fraudStatus === 'challenge') {
                    $pesanan->status = 'diproses';
                } else if ($fraudStatus === 'accept') {
                    $pesanan->status = 'selesai';
                }
                break;

            case 'settlement':
                $pesanan->status = 'selesai';
                break;

            case 'pending':
                if ($input['payment_type'] === 'credit_card' && $fraudStatus === 'accept') {
                    $pesanan->status = 'selesai';
                } else {
                    $pesanan->status = 'pending';
                }
                break;

            case 'deny':
            case 'expire':
            case 'cancel':
                $pesanan->status = 'gagal';
                break;

            default:
                $pesanan->status = 'pending';
                break;
        }

        $pesanan->save();

        Log::info('Midtrans callback processed', [
            'order_id' => $realId,
            'status'   => $pesanan->status,
        ]);

        return response()->json(['message' => 'Callback processed']);
    }
}
