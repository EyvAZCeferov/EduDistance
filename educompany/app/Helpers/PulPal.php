<?php

namespace App\Helpers;

use GuzzleHttp\Client;

class PulPal
{
    private $public_key = "04a1a8aa-5e67-4e81-86ae-3b2cb4655346";
    private $private_key = "x7wOAC3P/8OdoMwY7kYCzScWC/nWl52fF2aQqx+YlfuhRw8/UV/S9BtSzxo9oZVxTr2W1YHtLYRCuPbt3jp8AA==";
    private $merchant_id = 3933;
    private $host = 'https://payment-api.pulpal.az';
    private $path = '/api/merchant_payment/External/product/info?externalId=';
    private $nonce = '';
    private $external_id = '';
    public function __construct($nonce, $external_id)
    {
        $this->nonce = $nonce;
        $this->external_id = $external_id;
        $this->path .= $external_id;
    }
    public function generateSignature(): string
    {
        return base64_encode(hash_hmac('sha256', $this->public_key . $this->nonce . $this->path, base64_decode($this->private_key), true));
    }

    public function generateUrl(): string
    {
        return $this->host . $this->path;
    }
    public function generateHeaders(): array
    {
        return array(
            'Accept: text/plain',
            'Merchant-Id: ' . $this->merchant_id,
            'Api-Key: ' . $this->public_key,
            'Signature: ' . $this->generateSignature(),
            'Nonce: ' . $this->nonce,
        );
    }

    public function getStatus()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->generateUrl());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->generateHeaders());
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function a()
    {
        $pulpal = new PulPal(55555, 22101993);

        echo $pulpal->getStatus();
    }
}
