<?php


namespace RocketPayments\Payments\Requests;

use RocketPayments\Payments\IOrder as Order;

class PagSeguroSlipRecurrence extends RequestAbstract
{
    const METHOD = 'POST';
    const URL = 'https://ws.pagseguro.uol.com.br/recurring-payment/boletos?';
    const URL_SANDBOX = 'https://ws.sandbox.pagseguro.uol.com.br/recurring-payment/boletos?';

    public function config(Order $order = null) :array
    {
        $body = json_encode($order->getBody());
        $body = str_replace('\\', '', $body);
        $body = mb_substr($body, 1, -1);
        return [
            'body'=>$body,
            'headers'=>[
                'Content-Type' => 'application/json;charset=ISO-8859-1',
                'Accept' => 'application/json;charset=ISO-8859-1',
            ]
        ];
    }
}
