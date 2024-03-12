<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->group(['prefix' => 'v1'], function () use ($router) {
    $router->group(["prefix" => "cron"], function () use ($router) {
        $router->get('/processar', 'Rotina@processar');
    });
    $router->group(["prefix" => "pix"], function () use ($router) {
        $router->post("/config/webhook", "Efi@configWebHook");
        $router->post("/cob", "Efi@split");
        $router->post("/webhook/log", "Rotina@salvarLog");
    });
});
