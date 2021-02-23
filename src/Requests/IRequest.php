<?php


namespace RocketPayments\Payments\Requests;

use RocketPayments\Payments\IOrder as Order;

interface IRequest
{
    public function getUrl(Order $order, bool $sandbox = false):string;
    public function getMethod():string;
    public function config(Order $order = null):array;
}
