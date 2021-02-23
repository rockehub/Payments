<?php

namespace RocketPayments\Payments;

use RocketPayments\Payments\Requests\IRequest as Request;
use RocketPayments\Payments\IOrder as Order;
use GuzzleHttp\Client;

class MakeRequest
{


    private $client;
    private $request;

    public function __construct(Request $request)
    {
        $this->client = new Client();
        $this->request = $request;
    }

    public function make(Order $order, bool $sandbox = false)
    {
        try {
            $response = $this->client->request(
                $this->request->getMethod(),
                $this->request->getUrl($order, $sandbox),
                $this->request->config($order)
            );
            return $response->getBody();
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            return $e->getResponse()->getBody()->getContents();
        }
    }
}
