<?php


namespace RocketPayments\Payments\Requests;

use RocketPayments\Payments\IOrder as Order;

abstract class RequestAbstract implements IRequest
{
    private $child_const;

    public function getUrl(Order $order, bool $sandbox = false): string
    {
        if ($sandbox) {
            return $this->getChildConstants('url_sandbox') . (string)$order;
        }
        return $this->getChildConstants('url') . (string)$order;
    }

    public function getMethod():string
    {
        return $this->getChildConstants('method');
    }

    private function getChildConstants($const)
    {

        if (!$this->child_const) {
            $child = get_class($this);
            $this->child_const = [
                'url' => constant($child . '::URL'),
                'url_sandbox' => constant($child . '::URL_SANDBOX'),
                'method' => constant($child . '::METHOD'),

            ];
        }
        return $this->child_const[$const];
    }
}
