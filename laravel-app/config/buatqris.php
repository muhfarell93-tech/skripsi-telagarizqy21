<?php

return [
    'api_url'      => env('BUATQRIS_API_URL', 'https://api.buatqris.site'),
    'account_id'   => env('BUATQRIS_ACCOUNT_ID', ''),
    'secret_token' => env('BUATQRIS_SECRET_TOKEN', ''),
    'qris_method'  => env('BUATQRIS_METHOD', 'qris_two'),
    'fee_by'       => env('BUATQRIS_FEE_BY', 'user'),
    'is_sandbox'   => env('BUATQRIS_IS_SANDBOX', true),
];
