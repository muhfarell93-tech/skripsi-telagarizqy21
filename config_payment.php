<?php
/**
 * Konfigurasi Payment Gateway Midtrans (Sandbox & Production)
 * Sistem Informasi Transaksi Keuangan Telaga Rizqy 21
 */

return [
    // Mode environment: 'sandbox' (pengujian/tugas akhir) atau 'production' (operasional riil)
    'environment' => getenv('PAYMENT_ENV') ?: 'sandbox',

    // Kredensial Midtrans Sandbox
    'sandbox' => [
        'merchant_id' => getenv('MIDTRANS_SANDBOX_MERCHANT_ID') ?: 'G123456789',
        'client_key'  => getenv('MIDTRANS_SANDBOX_CLIENT_KEY')  ?: 'SB-Mid-client-XXXXXX',
        'server_key'  => getenv('MIDTRANS_SANDBOX_SERVER_KEY')  ?: 'SB-Mid-server-YYYYYY',
        'snap_url'    => 'https://app.sandbox.midtrans.com/snap/snap.js',
        'api_url'     => 'https://api.sandbox.midtrans.com/v2',
        'snap_api_url'=> 'https://app.sandbox.midtrans.com/snap/v1/transactions',
    ],

    // Kredensial Midtrans Production
    'production' => [
        'merchant_id' => getenv('MIDTRANS_PROD_MERCHANT_ID') ?: '',
        'client_key'  => getenv('MIDTRANS_PROD_CLIENT_KEY')  ?: '',
        'server_key'  => getenv('MIDTRANS_PROD_SERVER_KEY')  ?: '',
        'snap_url'    => 'https://app.midtrans.com/snap/snap.js',
        'api_url'     => 'https://api.midtrans.com/v2',
        'snap_api_url'=> 'https://app.midtrans.com/snap/v1/transactions',
    ],

    // Metode pembayaran online yang diaktifkan di sistem Telaga Rizqy 21
    'enabled_payments' => [
        'qris',           // QRIS (GoPay, ShopeePay, Dana, LinkAja, BCA Mobile, dll.)
        'gopay',          // GoPay DeepLink & QR
        'shopeepay',      // ShopeePay
        'bca_va',         // Virtual Account BCA
        'bni_va',         // Virtual Account BNI
        'bri_va',         // Virtual Account BRI
        'mandiri_bill',   // Mandiri Bill Payment
        'permata_va',     // Virtual Account Permata
    ],

    // Durasi batas waktu pembayaran (Expiry Duration) dalam menit
    'expiry_duration' => 60, // 60 menit (1 jam)
    'expiry_unit'     => 'minute',
];

