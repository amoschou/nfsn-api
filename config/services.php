<?php

return [
    'nfsn' => [
        'login' => env('NFSN_API_LOGIN'),
        'api_key' => env('NFSN_API_KEY'),
        'server_name' => env('NFSN_API_SEVER_NAME', 'api.nearlyfreespeech.net'),
        'protocol' => env('NFSN_API_PROTOCOL', 'https'),
        'dns_id' => env('NFSN_DNS_ID'),
        'dns_subdomain' => env('NFSN_DNS_SUBDOMAIN')
    ],
];
