<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\Logs;
use App\Models\OrderList;
use App\Http\Controllers\Efi;
use Exception;

class Rotina extends BaseController
{
    public function processar()
    {
        $dataHoraInicio = date('Y-m-d H:i:s');

        $list = OrderList::where('status', 2)
            ->where('payment_method', 'Gerencianet')
            ->where('split_status', null)
            ->get();


        $list = [
            [
                "id" => 46,
                "total_amount" => '4.00'
            ],
            // [
            //     "id" => 62,
            //     "total_amount" => '5.00'
            // ],
            // [
            //     "id" => 63,
            //     "total_amount" => '15.50'
            // ]
        ];

        if (count($list)) {
            $efi = new Efi();
            foreach ($list as $item) {
                $split = $efi->split($item);

                if (isset($split['status'])) {

                    $json = [
                        "pagamento" => $split
                    ];

                    OrderList::where('id', $item['id'])->update(['split_status' => $split['status'], 'split_infor' => json_encode($json, 256)]);
                } else {
                    $json = [
                        "pagamento" => $split
                    ];
                    OrderList::where('id', $item['id'])->update(['split_status' => 'ERROR', 'split_infor' => json_encode($json, 256)]);
                }
            }
        }

        $dataHoraFim = date('Y-m-d H:i:s');

        $logc = "Início da tarefa cron - " . $dataHoraInicio . " - Fim da tarefa cron - " . $dataHoraFim;

        $log = new Logs();
        $log->origin = 'Cron';
        $log->description = $logc;
        $log->date = date('Y-m-d H:i:s');
        $log->save();

        echo 'sucesso';
        return true;
    }

    public function notificacao(Request $request)
    {
        $postEfi = $request->all();

        $log = new Logs();
        $log->origin = 'Post - Efí';
        $log->description = json_encode($postEfi, 256);
        $log->date = date('Y-m-d H:i:s');
        $log->save();

        try {
            if (isset($postEfi['pix'])) {
                if (count($postEfi['pix'])) {
                    $notificacao = $postEfi['pix'][0];

                    if (isset($notificacao['gnExtras']['idEnvio'])) {

                        $orderList = OrderList::where('id', $notificacao['gnExtras']['idEnvio'])->get()->first();

                        $json = json_decode($orderList['split_infor'], true);

                        $array = [];
                        $array['notificacao'] = $postEfi;
                        $array['pagamento'] = $json['pagamento'];

                        $json = json_encode($array, 256);

                        OrderList::where('id', $notificacao['gnExtras']['idEnvio'])->update(["split_status" => $notificacao['status'], "split_infor" => $json, 'split_data_notify' => date('Y-m-d H:i:s')]);
                    }
                }
            }
        } catch (Exception $e) {
            // print_r($e->getMessage());
            return ['msg' => $e->getMessage()];
        }
    }

    public function salvarLog(Request $request)
    {
        $postEfi = $request->all();

        $log = new Logs();
        $log->origin = 'Post - Efí';
        $log->description = json_encode($postEfi, 256);
        $log->date = date('Y-m-d H:i:s');
        $log->save();
    }
}
