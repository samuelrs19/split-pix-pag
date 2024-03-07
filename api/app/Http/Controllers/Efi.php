<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Efi\Exception\EfiException;
use Efi\EfiPay;

class Efi extends BaseController
{
    public function envio()
    {
        $params = [
            "idEnvio" => "00000000000000000000000000000000000"
        ];

        $body = [
            "valor" => "0.01",
            "pagador" => [
                "chave" => "00000000-0000-0000-0000-000000000000", // Pix key registered in the authenticated Efí account
                "infoPagador" => "Order payment"
            ],
            "favorecido" => [
                "chave" => "Receiver_Pix_key" // Type key: random, email, phone, cpf or cnpj
            ]
        ];

        try {
            $api = new EfiPay($options);
            $response = $api->pixSend($params, $body);

            print_r("<pre>" . json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "</pre>");
        } catch (EfiException $e) {
            print_r($e->code . "<br>");
            print_r($e->error . "<br>");
            print_r($e->errorDescription) . "<br>";
        } catch (Exception $e) {
            print_r($e->getMessage());
        }
    }
}
