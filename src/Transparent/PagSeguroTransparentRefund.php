<?php


namespace RocketPayments\Payments\Transparent;

use RocketPayments\Payments\IOrder;

class PagSeguroTransparentRefund implements IOrder
{
    private $refund;

    private $config;

    public function __construct(string $email, string $token)
    {
        $this->config = [
            'email' => $email,
            'token' => $token,
        ];
    }


    public function setRefund(...$refund)
    {
        $this->refund = [
            'transactionCode'=>$refund[0],
            'refundValue'=>$refund[1]
        ];
    }

    public function getBody()
    {
        return http_build_query($this->refund);
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
