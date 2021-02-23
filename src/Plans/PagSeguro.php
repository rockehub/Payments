<?php


namespace RocketPayments\Payments\Plans;

use RocketPayments\Payments\IOrder;

class PagSeguro implements IOrder
{
    private $config;
    private $plan;

    public function __construct(string $email, string $token)
    {
        $this->config = [
            'email'=>$email,
            'token'=>$token
        ];
    }

    public function setPlan(array $plan)
    {
        $this->plan = $plan;
    }

    public function getBody()
    {
        return $this->plan;
    }

    public function __toString() :string
    {
        $access = [
            'email'=>$this->config['email'],
            'token'=>$this->config['token'],
        ];

        return http_build_query($access);
    }
}
