<?php


namespace RocketPayments\Payments\Slip;

use RocketPayments\Payments\IOrder;

class PagSeguroPaymentSlip implements IOrder
{
    private $infos;
    private $sender;
    private $shipping;
    private $products = [];
    private $config;

    public function __construct(string $email, string $token)
    {
        $this->config = [
            'email' => $email,
            'token' => $token,
        ];
    }

    public function infos(...$info)
    {

        $this->infos = [
            'paymentMode' => $info[0],
            'hash' => $info[1],
            'paymentMethod' => $info[2],
            'receiverEmail' => $info[3],
            'currency' => $info[4],
            'shippingAddressRequired' => $info[5],
            'reference' => $info[6],
            "firstDueDate" => $info[7],
            "numberOfPayments" => $info[8],
            "periodicity" => $info[9],


        ];
    }

    public function customer(...$sender)
    {
        $this->sender = [
            'senderName' => $sender[0],
            'senderAreaCode' => $sender[1],
            'senderPhone' => $sender[2],
            'senderEmail' => $sender[3],
            'senderCPF' => $sender[4],
        ];
    }

    public function shipping(...$shipping)
    {
        $this->shipping = [
            'shippingAddressStreet' => $shipping[0],
            'shippingAddressNumber' => $shipping[1],
            'shippingAddressComplement' => $shipping[2],
            'shippingAddressDistrict' => $shipping[3],
            'shippingAddressPostalCode' => $shipping[4],
            'shippingAddressCity' => $shipping[5],
            'shippingAddressState' => $shipping[6],
            'shippingAddressCountry' => $shipping[7],
            'shippingType' => $shipping[8],
            'shippingCost' => $shipping[9],
        ];
    }

    public function addProduct(...$product)
    {
        $index = count($this->products);
        $this->products[$index] = [
            'id' => $product[0],
            'description' => $product[1],
            'amount' => $product[2],
            'quantity' => $product[3],
        ];
        if (!empty($product[4])) {
            $this->products[$index]['weight'] = $product[4];
        }
    }

    public function toArray()
    {
        $items = [];
        foreach ($this->products as $k => $product) {
            $counter = $k + 1;
            $items['itemId' . $counter] = $product['id'];
            $items['itemDescription' . $counter] = $product['description'];
            $items['itemAmount' . $counter] = number_format($product['amount'], 2, '.', '');
            $items['itemQuantity' . $counter] = $product['quantity'];
            if (!empty($product['weight'])) {
                $items['itemWeight' . $counter] = $product['weight'];
            }
        }
        if ($this->infos['shippingAddressRequired'] == 'true') {
            return array_merge($this->infos, $this->sender, $this->shipping, $items);
        } else {
            return array_merge($this->infos, $this->sender, $items);
        }
    }

    public function getBody()
    {
        return http_build_query($this->toArray());
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
