<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Efi\Exception\EfiException;
use Efi\EfiPay;
use Exception;
use App\Models\Credential_efi;
use App\Models\Pix_credential;

class Efi extends BaseController
{
    public function split($dadosPagamentos)
    {

        $options = $this->config();
        $configSplit = $this->configSplit();

        $params = [
            "idEnvio" => $dadosPagamentos['id']
        ];

        $valor = $dadosPagamentos['total_amount'];
        if ($configSplit['percentual'] > 0) {
            $valor = ($dadosPagamentos['total_amount'] * $configSplit['percentual']) / 100;
        }

        // $valor = round($valor, 2);
        $valor = number_format($valor, 2, '.', '');

        $body = [
            "valor" => "$valor",
            "pagador" => [
                "chave" => $configSplit['pagador']['chave'],
                "infoPagador" => $configSplit['pagador']['nome']
            ],
            "favorecido" => [
                "chave" => $configSplit['recebedor'][0]
            ]
        ];

        try {
            $api = new EfiPay($options);
            return $api->pixSend($params, $body);
        } catch (EfiException $e) {
            // print_r($e->code . "<br>");
            // print_r($e->error . "<br>");
            // print_r($e->errorDescription) . "<br>";
            return [
                'status' => 'ERROR',
                'statusCod' => $e->code,
                'msg' => $e->error,
                'descricao' => $e->errorDescription
            ];
        } catch (Exception $e) {
            // print_r($e->getMessage());
            return ['msg' => $e->getMessage()];
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

        $pathCertificateProd = realpath(__DIR__ . "/../../../storage/certificados/productionCertificate.p12"); // Absolute path to the certificate in .pem or .p12 format
        $pathCertificateHomolog = realpath(__DIR__ . "/../../../storage/certificados/developmentCertificate.p12"); // Absolute path to the certificate in .pem or .p12 format

        $crencials = Credential_efi::where('status', 1)->first();

        $clientId = "";
        $clientSecret = "";
        if ($crencials) {

            $crencials = json_encode($crencials, 256);
            $crencials = json_decode($crencials, true);

            $clientId = $crencials['client_Id'];
            $clientSecret = $crencials['client_Secret'];
            if ($crencials['environment'] == 'environment') {
                $sandbox = false;
            }
        }

        /**
         * Array with credentials for sending requests
         */

        return [
            "clientId" => $clientId,
            "clientSecret" => $clientSecret,
            "certificate" => ($sandbox) ? $pathCertificateHomolog : $pathCertificateProd,
            "pwdCertificate" => "", // Optional | Default = ""
            "sandbox" => $sandbox, // Optional | Default = false
            "debug" => false, // Optional | Default = false
            "timeout" => 30, // Optional | Default = 30
        ];
    }

    private function configSplit()
    {
        $return = [
            "pagador" => [
                "chave" => "",
                "nome" => ""
            ],
            "percentual" => 100,
            "recebedor" => []
        ];

        $confiPay = Pix_credential::where('status', 1)->first();

        if ($confiPay) {
            $confiPay = json_encode($confiPay, 256);
            $confiPay = json_decode($confiPay, true);

            $return['pagador']['chave'] = $confiPay['payer_key'];
            $return['pagador']['nome'] = $confiPay['payer_name'];
            $return['percentual'] = $confiPay['percentage'];

            $return['recebedor'][] = $confiPay['key_favored_one'];
            if (!empty($confiPay['key_favored_two'])) {
                $return['recebedor'][] = $confiPay['key_favored_two'];
            }
        }

        return $return;
    }
}
