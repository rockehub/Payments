<?php

namespace RocketPayments\Payments\Notifications;

use RocketPayments\Payments\IOrder;

class PagSeguro implements IOrder
{
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function __toString(): string
    {
        $access = [
            'email'=>$this->config['email'],
            'token'=>$this->config['token'],
        ];
        $http_query = http_build_query($access);
        return $this->config['notificationCode']. '?'.$http_query;
    }
}
