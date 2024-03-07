<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\Logs;
use App\Models\OrderList;

class Rotina extends BaseController
{
    public function processar()
    {
        $dataHoraInicio = date('Y-m-d H:i:s');

        $list = OrderList::where('status', 2)
            ->where('payment_method', 'Gerencianet')
            ->get();


        if (count($list)) {

            
        }

        $dataHoraFim = date('Y-m-d H:i:s');

        $logc = "Início da tarefa cron - " . $dataHoraInicio . " - Fim da tarefa cron - " . $dataHoraFim;

        $log = new Logs();
        $log->origin = 'Cron';
        $log->description = $logc;
        $log->date = date('Y-m-d H:i:s');
        $log->save();
    }
}
