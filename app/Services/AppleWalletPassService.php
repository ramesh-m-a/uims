<?php

namespace App\Services;

use PKPass\PKPass;

class AppleWalletPassService
{
    public function generate(array $userData): string
    {
        $pkpass = new PKPass(
            storage_path('certs/pass.p12'),   // your Apple cert
            env('APPLE_PASS_CERT_PASSWORD')
        );

        $data = [
            'description' => 'RGUHS Examinership ID',
            'formatVersion' => 1,
            'organizationName' => 'RGUHS',
            'passTypeIdentifier' => env('APPLE_PASS_TYPE_ID'),
            'serialNumber' => $userData['partials'],
            'teamIdentifier' => env('APPLE_TEAM_ID'),

            'generic' => [
                'primaryFields' => [
                    [
                        'key' => 'name',
                        'label' => 'Name',
                        'value' => $userData['name'],
                    ]
                ],
                'secondaryFields' => [
                    [
                        'key' => 'designation',
                        'label' => 'Designation',
                        'value' => $userData['designation'],
                    ]
                ]
            ],
            'barcode' => [
                'format' => 'PKBarcodeFormatQR',
                'message' => $userData['verify_url'],
                'messageEncoding' => 'iso-8859-1',
            ],
        ];

        $pkpass->setData($data);

        // Add images
        $pkpass->addFile(public_path('images/wallet/icon.png'));
        $pkpass->addFile(public_path('images/wallet/logo.png'));

        if (! $pkpass->create()) {
            throw new \Exception($pkpass->getError());
        }

        return $pkpass->get();
    }
}
