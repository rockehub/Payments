<?php


namespace RocketPayments\Payments\Requests;

use RocketPayments\Payments\IOrder as Order;

class PagSeguro extends RequestAbstract
{
    const URL = "https://ws.pagseguro.uol.com.br/v2/checkout?";
    const URL_SANDBOX = "https://ws.sandbox.pagseguro.uol.com.br/v2/checkout?";
    const METHOD = 'POST';

    const URL_FINAL = 'https://pagseguro.uol.com.br/v2/checkout/payment.html';
    const URL_FINAL_SANDBOX = 'https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html';


    public function getUrlFinal($code, bool $sandbox = false)
    {
        if ($sandbox) {
            return PagSeguro::URL_FINAL_SANDBOX . '?code=' . (string)$code;
        }
        return PagSeguro::URL_FINAL . '?code=' . (string)$code;
    }

    public function config(Order $order = null): array
    {

        return  [
            'form_params' => []
        ];
    }
}
