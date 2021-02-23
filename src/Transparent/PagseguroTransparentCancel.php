<?php


namespace RocketPayments\Payments\Transparent;

use RocketPayments\Payments\IOrder;

class PagseguroTransparentCancel implements IOrder
{
    private $transaction;

    private $config;

    public function __construct(string $email, string $token)
    {
        $this->config = [
            'email' => $email,
            'token' => $token,
        ];
    }


    public function setTransaction($transaction)
    {
        $this->transaction = [
            'transactionCode'=>$transaction
        ];
    }

    public function getBody()
    {
        return http_build_query($this->transaction);
    }

    public function __toString(): string
    {
        $access = [
            'email' => $this->config['email'],
            'token' => $this->config['token'],
        ];
        return http_build_query($access);
    }
}
