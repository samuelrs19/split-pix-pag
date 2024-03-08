<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Efi\Exception\EfiException;
use Efi\EfiPay;
use Exception;

class Efi extends BaseController
{
    public function envio()
    {
        $options = $this->config();

        $params = [
            "idEnvio" => "0000000000000000000000000"
        ];

        $body = [
            "valor" => "0.01",
            "pagador" => [
                "chave" => "47375685000114", // Pix key registered in the authenticated Efí account
                "infoPagador" => "Order payment"
            ],
            "favorecido" => [
                "chave" => "efipay@sejaefi.com.br" // Type key: random, email, phone, cpf or cnpj
            ]
        ];

        try {
            $api = new EfiPay($options);
            $response = $api->pixSend($params, $body);

            echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (EfiException $e) {
            print_r($e->code . "<br>");
            print_r($e->error . "<br>");
            print_r($e->errorDescription) . "<br>";
        } catch (Exception $e) {
            print_r($e->getMessage());
        }
    }

    public function configWebHook()
    {
        $options = $this->config();


        $options["headers"] = [
            "x-skip-mtls-checking" => "true",
        ];

        $params = [
            "chave" => "47375685000114"
        ];

        $body = [
            "webhookUrl" => "https://olhonopremio.com/webhook.php"
        ];

        try {
            $api = new EfiPay($options);
            $response = $api->pixConfigWebhook($params, $body);

            print_r("<pre>" . json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "</pre>");
        } catch (EfiException $e) {
            print_r($e->code . "<br>");
            print_r($e->error . "<br>");
            print_r($e->errorDescription) . "<br>";
        } catch (Exception $e) {
            print_r($e->getMessage());
        }

    }

    private function config()
    {
        $sandbox = true; // false = Production | true = Homologation

        /**
         * Credentials of Production
         */
        $clientIdProd = "Client_Id_ecdcf900e56b743b6fe9f68b5942388e5080e835";
        $clientSecretProd = "Client_Secret_b033cc9b8f060754ac3d8ad51dffe29e4863e409";
        $pathCertificateProd = realpath(__DIR__ . "/../../../storage/certificados/productionCertificate.p12"); // Absolute path to the certificate in .pem or .p12 format

        /**
         * Credentials of Homologation
         */
        $clientIdHomolog = "Client_Id_2cac2c436caa25fb149fb1ea200d380f9d4ec07f";
        $clientSecretHomolog = "Client_Secret_7a30faf66365cbb0c48abd042cf2b79cb8dcda07";
        $pathCertificateHomolog = realpath(__DIR__ . "/../../../storage/certificados/developmentCertificate.p12"); // Absolute path to the certificate in .pem or .p12 format

        /**
         * Array with credentials for sending requests
         */
        $options = [
            "clientId" => ($sandbox) ? $clientIdHomolog : $clientIdProd,
            "clientSecret" => ($sandbox) ? $clientSecretHomolog : $clientSecretProd,
            "certificate" => ($sandbox) ? $pathCertificateHomolog : $pathCertificateProd,
            "pwdCertificate" => "", // Optional | Default = ""
            "sandbox" => $sandbox, // Optional | Default = false
            "debug" => false, // Optional | Default = false
            "timeout" => 30, // Optional | Default = 30
        ];

        return [
            "clientId" => ($sandbox) ? $clientIdHomolog : $clientIdProd,
            "clientSecret" => ($sandbox) ? $clientSecretHomolog : $clientSecretProd,
            "certificate" => ($sandbox) ? $pathCertificateHomolog : $pathCertificateProd,
            "pwdCertificate" => "", // Optional | Default = ""
            "sandbox" => $sandbox, // Optional | Default = false
            "debug" => false, // Optional | Default = false
            "timeout" => 30, // Optional | Default = 30
        ];
    }
}
