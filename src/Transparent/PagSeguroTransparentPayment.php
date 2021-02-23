<?php


namespace RocketPayments\Payments\Transparent;

use RocketPayments\Payments\IOrder;

class PagSeguroTransparentPayment implements IOrder
{
    private $config;
    private $sender;
    private $shipping;
    private $billing;
    private $payment;
    private $instalment;
    private $infos;
    protected $products = [];

    public function __construct(string $email, string $token)
    {
        $this->config = [
            'email' => $email,
            'token' => $token,
        ];
    }

    public function info(...$info)
    {

        $this->infos = [
            'paymentMethod' => $info[0],
            'mode' => $info[1],
            'reference' => $info[2],
            'senderHash' => $info[3],
            'receiverEmail' => $info[4],
            'creditCardToken' => $info[5],
            'shippingAddressRequired' => $info[6]

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

    public function customer(...$customer)
    {
        $this->sender = [
            'senderName' => $customer[0],
            'senderAreaCode' => $customer[1],
            'senderPhone' => $customer[2],
            'senderEmail' => $customer[3],
            'senderCPF' => $customer[4],
        ];
    }

    public function installment(...$instalment)
    {
        $this->instalment = [
            'installmentQuantity' => $instalment[0],
            'noInterestInstallmentQuantity' => $instalment[1],
            'installmentValue' => $instalment[2],

        ];
    }

    public function billing(...$billing)
    {
        $this->billing = [
            'billingAddressStreet' => $billing[0],
            'billingAddressNumber' => $billing[1],
            'billingAddressDistrict' => $billing[2],
            'billingAddressPostalCode' => $billing[3],
            'billingAddressCity' => $billing[4],
            'billingAddressState' => $billing[5],
            'billingAddressCountry' => $billing[6],
            'currency' => $billing[7]
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

    public function paymentMethod(...$payment)
    {
        $this->payment = [
            'creditCardHolderName' => $payment[0],
            'creditCardHolderCPF' => $payment[1],
            'creditCardHolderBirthDate' => $payment[2],
            'creditCardHolderAreaCode' => $payment[3],
            'creditCardHolderPhone' => $payment[4]
        ];
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
            return array_merge($this->infos, $this->sender, $this->instalment, $this->payment, $this->billing, $this->shipping, $items);
        } else {
            return array_merge($this->infos, $this->sender, $this->instalment, $this->payment, $this->billing, $items);
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
