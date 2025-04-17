<?php

namespace App\Services;

use Midtrans\Snap;
use Midtrans\Config;

class MidtransService {
    public function __construct() {
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function createTransaction($params) {
        return Snap::createTransaction($params);
    }
}
