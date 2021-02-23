<?php

namespace RocketPayments\Payments;

use RocketPayments\Payments\Notifications\PagSeguro as Notification;
use RocketPayments\Payments\Payments\PagSeguro as Payment;
use RocketPayments\Payments\Requests\PagSeguro as Request;

//========================recorrencia =================
use RocketPayments\Payments\Recurrence\PagSeguro as Recurrence;
use RocketPayments\Payments\Requests\PagSeguroNotification as RequestNotification;
use RocketPayments\Payments\Requests\PagSeguroRecurrence as RecurrenceRequest;
use RocketPayments\Payments\Plans\PagSeguro as Plans;
use RocketPayments\Payments\requests\PagSeguroPlanCreation as RequestPlans;

//========================pagamento transparente =================
use RocketPayments\Payments\Transparent\PagSeguroTransparentPayment as Transparent;
use RocketPayments\Payments\Requests\PagSeguroTransparentPayment as TransparentRequest;
use RocketPayments\Payments\Transparent\PagseguroTransparentCancel as TransparentCancel;
use RocketPayments\Payments\Requests\PagseguroTransparentCancel as TransparentCancelRequest;
use RocketPayments\Payments\Transparent\PagSeguroTransparentRefund as TransparentRefund;
use RocketPayments\Payments\Requests\PagSeguroTransparentRefund as TransparentRefundRequest;
use RocketPayments\Payments\MakeRequest;

//============================ slipPayment ========================
use RocketPayments\Payments\Slip\PagSeguroPaymentSlip as Slip;
use RocketPayments\Payments\Requests\PagseguroPaymentSlip as SlipRequest;

//=========================== sliprecurrence ======================
use RocketPayments\Payments\SlipRecurrence\PagSeguroSlipRecurrence as SlipRecurrence;
use RocketPayments\Payments\Requests\PagSeguroSlipRecurrence as SlipRecurrenceRequest;

class PagSeguro
{
    private $email;
    private $token;
    private $sandbox;

    public function __construct(string $email, string $token, bool $sandbox = false)
    {
        $this->email = $email;
        $this->token = $token;
        $this->sandbox = $sandbox;
    }

    //exercicio - criar um método para criação de planos

    public function payment(string $reference, array $customer, array $shipping, array $products, string $currency = "BRL")
    {
        $payment = new Payment([
            'email' => $this->email,
            'token' => $this->token,
            'currency' => $currency,
            'reference' => $reference
        ]);
        call_user_func_array([$payment, 'customer'], $customer);
        call_user_func_array([$payment, 'shipping'], $shipping);
        foreach ($products as &$product) {
            call_user_func_array([$payment, 'addProduct'], $product);
        }

        $request = new Request();
        $response = (new MakeRequest($request))->make($payment, $this->sandbox);
        $xml = new \SimpleXMLElement((string)$response);
        return [
            'xml' => $xml,
            'url' => $request->getUrlFinal($xml->code, $this->sandbox)
        ];
    }

    public function slipPayment(array $customer, array $products, array $infos, array $shipping)
    {
        $slippayment = new Slip($this->email, $this->token);
        call_user_func_array([$slippayment, 'customer'], $customer);
        foreach ($products as &$product) {
            call_user_func_array([$slippayment, 'addProduct'], $product);
        }
        call_user_func_array([$slippayment, 'infos'], $infos);
        call_user_func_array([$slippayment, 'shipping'], $shipping);
        $request = new SlipRequest;
        return (new MakeRequest($request))->make($slippayment, $this->sandbox);
    }

    public function slipRecurrence(array $info, array $sender, array $shipping)
    {
        $sliprecurrence = new SlipRecurrence($this->email, $this->token);
        call_user_func_array([$sliprecurrence, 'customer'], $sender);
        call_user_func_array([$sliprecurrence, 'shipping'], $shipping);
        call_user_func_array([$sliprecurrence, 'info'], $info);

        $request = new SlipRecurrenceRequest;
        return (new MakeRequest($request))->make($sliprecurrence, $this->sandbox);
    }

    public function transparentPayment(array $customer, array $products, array $instalment, array $billing, array $payment, array $infos, array $shipping = null)
    {
        $transparentpayment = new transparent($this->email, $this->token);
        call_user_func_array([$transparentpayment, 'customer'], $customer);
        foreach ($products as &$product) {
            call_user_func_array([$transparentpayment, 'addProduct'], $product);
        }
        call_user_func_array([$transparentpayment, 'info'], $infos);
        call_user_func_array([$transparentpayment, 'installment'], $instalment);
        call_user_func_array([$transparentpayment, 'billing'], $billing);
        call_user_func_array([$transparentpayment, 'PaymentMethod'], $payment);
        call_user_func_array([$transparentpayment, 'shipping'], $shipping);
        $request = new TransparentRequest;
        return (new MakeRequest($request))->make($transparentpayment, $this->sandbox);
    }

    public function cancel(string $transanctioncode)
    {
        $transparentcancel = new TransparentCancel($this->email, $this->token);
        call_user_func([$transparentcancel, 'setTransaction'], $transanctioncode);
        $request = new  TransparentCancelRequest;
        return (new MakeRequest($request))->make($transparentcancel, $this->sandbox);
    }

    public function refund(array $refund)
    {
        $transparentrefund = new TransparentRefund($this->email, $this->token);
        call_user_func_array([$transparentrefund, 'setRefund'], $refund);
        $request = new TransparentRefundRequest;
        return (new MakeRequest($request))->make($transparentrefund, $this->sandbox);
    }

    public function notification(string $notificationCode)
    {
        $notification = new Notification([
            'email' => $this->email,
            'token' => $this->token,
            'notificationCode' => $notificationCode
        ]);
        $request = new RequestNotification;
        return (new MakeRequest($request))->make($notification, $this->sandbox);
    }

    public function createPlan(array $plan)
    {
        $createplan = new Plans($this->email, $this->token);
        call_user_func([$createplan, 'setPlan'], $plan);
        $request = new RequestPlans;
        return (new MakeRequest($request))->make($createplan, $this->sandbox);
    }

    public function recurrence(string $plan, string $reference, array $customer, array $shipping, array $payment)
    {
        $recurrence = new Recurrence($this->email, $this->token, $plan, $reference);
        call_user_func_array([$recurrence, 'customer'], $customer);
        call_user_func_array([$recurrence, 'shipping'], $shipping);
        call_user_func_array([$recurrence, 'paymentMethod'], $payment);

        $request = new RecurrenceRequest;
        return (new MakeRequest($request))->make($recurrence, $this->sandbox);
    }

    public function session()
    {
        $access = [
            'email' => $this->email,
            'token' => $this->token
        ];
        $url = 'https://ws.pagseguro.uol.com.br/v2/sessions';
        if ($this->sandbox) {
            $url = 'https://ws.sandbox.pagseguro.uol.com.br/v2/sessions';
        }
        $url .= '?' . http_build_query($access);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}
