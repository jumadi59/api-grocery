<?php

// override core en language system validation or define your own en language validation message
return [
    'day' => 'Hari',
    'coupons'       => [
        'discount'          => 'Diskon',
        'cashback'          => 'Cashback',
        'free_shipping'     => 'Gratis Ongkir'
    ],
    'auth'          => [
        'valid_email'       => '',
        'valid_password'    => '',
        'valid_phone'       => ''
    ],
    'user' => [
        'valid_username'    => ''
    ],
    'order' => [
        'confirmation'      => 'Konfirmasi',
        'packed'            => 'Paked',
        'sent'              => 'Dikirim',
        'done'              => 'Selesai',
        'canceled'          => 'Dibatal',
        'expire'            => 'Expire',
        'taking'            => 'Taking'
    ],
    'transaction' => [
        'pending'           => 'Pending',
        'settlement'        => 'Settlement',
        'expire'            => 'Expire',
        'cancel'            => 'Batal',
        'deny'              => 'Deny',
        'title_pending'     => 'Pesanan anda telah di buat',
        'msg_pending'       => 'Pesana anda telah di buat, silahkan melakuakan pembayaran, sebelum tanggal {datetime}',
        'title_settlement'  => 'Checkout Pesanan dengan {bank_name} berhasil tanggal {datetime}',
        'msg_settlement'    => 'Pembayaran anda telah kami terima, pesanan anda di telah kami teruskan ke penjual',
        'title_expire'      => 'Transaction expired',
        'msg_expire'        => 'Transaksi dengan {bank_name} dibatalkan karena telah lewat batas pembayaran',
        'title_cancel'      => 'Transaction canceled',
        'msg_cancel'        => 'Transaksi telah dibatalkan',
        'title_deny'        => 'Transaction deny',
        'msg_deny'          => 'Transaksi ditolak',
    ]
];
