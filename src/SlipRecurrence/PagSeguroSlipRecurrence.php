<?php


namespace RocketPayments\Payments\SlipRecurrence;

use RocketPayments\Payments\IOrder;

class PagSeguroSlipRecurrence implements IOrder
{


    private $config;
    private $sender;
    private $shipping;
    private $info;

    public function __construct(string $email, string $token)
    {
        $this->config = [
            'email' => $email,
            'token' => $token,
        ];
    }

    public function info(...$info)
    {
        $this->info = [
            "reference" => $info[0],
            "firstDueDate" => $info[1],
            "numberOfPayments" => $info[2],
            "periodicity" => $info[3],
            "amount" => $info[4],
            "instructions" => $info[5],
            "description" => $info[6],
        ];
    }

    public function customer(...$customer)
    {
        $this->sender = [
            'document' =>[
                    'type' => 'CPF',
                    'value' => $customer[4]
                ],
            'name' => $customer[0],
            'email' => $customer[1],
            'phone' => [
                'areaCode' => $customer[2],
                'number' => $customer[3],
            ],

        ];
    }

    public function shipping(...$shipping)
    {
        $this->shipping = [
            'city' => $shipping[0],
            'district' => $shipping[1],
            'number' => $shipping[2],
            'postalCode' => $shipping[3],
            'state' => $shipping[4],
            'street' => $shipping[5]

        ];
    }

    public function getBody()
    {
        $data = [
            'reference' => $this->info['reference'],
            'firstDueDate' => $this->info['firstDueDate'],
            'numberOfPayments' => $this->info['numberOfPayments'],
            'periodicity' => $this->info['periodicity'],
            'amount' => $this->info['amount'],
            'instructions' => $this->info['instructions'],
            'description' => $this->info['description'],
            'customer' => $this->sender,
        ];

        $data['customer']['address'] = $this->shipping;

        return json_encode($data);
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
