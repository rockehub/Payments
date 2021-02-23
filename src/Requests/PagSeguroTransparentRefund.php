<?php


namespace RocketPayments\Payments\Requests;

use RocketPayments\Payments\IOrder as Order;

class PagSeguroTransparentRefund extends RequestAbstract
{

    const METHOD = 'POST';
    const URL = 'https://ws.pagseguro.uol.com.br/v2/transactions/cancels?';
    const URL_SANDBOX = 'https://ws.sandbox.pagseguro.uol.com.br/v2/transactions/cancels?';

    public function config(Order $order = null): array
    {
        return [
            'body' => $order->getBody(),
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded; charset=ISO-8859-1'
            ]
        ];
    }
}
