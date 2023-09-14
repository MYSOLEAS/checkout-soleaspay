<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpClient\HttpClient;

class MainController extends AbstractController
{
    #[Route('/checkout', name: 'checkout')]
    public function checkout(Request $request): Response
    {
        $content = $request->getContent();
        $data = json_decode($content, true);

        if (empty($data)) {
            $data = $request->request->all();
        }
        $amount =   $data['amount'] ?? null;
        $currency  = $data['currency'] ?? null;
        $description  = $data['description'] ?? null;
        $orderId = $data['orderId'] ?? null;
        $apiKey  = $data['apiKey'] ?? null;
        $service  = $data['service'] ?? null;
        $successUrl  = $data['successUrl'] ?? null;
        $failureUrl  = $data['failureUrl'] ?? null;
        $shopName  = $data['shopName'] ?? null;


        $emptyElements = [];

        $elements = [
            'amount' => $amount,
            'currency' => $currency,
            'description' => $description,
            'orderId' => $orderId,
            'apiKey' => $apiKey,
            'shopName' => $shopName,
            'successUrl' => $successUrl,
            'failureUrl' => $failureUrl
        ];

        foreach ($elements as $key => $value) {
            if (empty($value)) {
                $emptyElements[$key] = true;
            }
        }

        if (!empty($emptyElements)) {
            return $this->render('main/error_params.html.twig', [
                'emptyElements' => $emptyElements
            ]);
        }


        setcookie('amount', $amount);
        setcookie('currency', $currency);
        setcookie('description', $description);
        setcookie('orderId', $orderId);
        setcookie('apiKey', $apiKey);
        setcookie('service', $service);
        setcookie('successUrl', $successUrl);
        setcookie('failureUrl', $failureUrl);
        setcookie('shopName', $shopName);

        return $this->render('main/index.html.twig', [
            'amount' => $amount,
            'description' => $description,
            'orderId' => $orderId,
            'currency' => $currency,
            'service' => $service,
            'shopName' => $shopName
        ]);
    }

    #[Route('/sendToApi', name: 'sendToApi')]
    public function sendToApi(Request $request): Response
    {
        if ($request->isMethod('POST')) {

            $services = [
                'orange_money_CM' => 2,
                'mtn_mobile_money_CM' => 1,
                'bitcoin' => 3,
                'paypal' => 7,
                'express_union' => 5,
                'perfect_money' => 8,
                'litecoin' => 10,
                'dogecoin' => 11,
                'visa' => 23,
                'mastercard' => 23,
            ];

            $operation = 2;

            if (empty($_COOKIE['service'])) {
                $service = $_POST['radio-button'];
            } else {
                $service = $_COOKIE['service'];
            }

            if ($service == 'paypal') {
                $wallet = $_POST['email_paypal'];
            } elseif ($service == 'orange_money_CM' || $service == 'mtn_mobile_money_CM' || $service == 'express_union') {
                $wallet = $_POST['number'];
            } else {
                $wallet = '';
            }

            $client = HttpClient::create();
            $response = $client->request('POST', 'https://soleaspay.com/api/agent/bills', [
                'headers' => [
                    'x-api-key' => $_COOKIE['apiKey'] ?? null,
                    'service' => $services[$service],
                    'operation' => $operation,
                ],
                'json' => [
                    'wallet' => $wallet,
                    'amount' => $_COOKIE['amount'] ?? null,
                    'currency' => $_COOKIE['currency'] ?? null,
                    'order_id' => $_COOKIE['orderId'] ?? null,
                    'success_url' => $_COOKIE['successUrl'] ?? null,
                    'failure_url' => $_COOKIE['failureUrl'] ?? null,
                ],
            ]);
        }

        $responseContent = json_decode($response->getContent(), true);
        if ($response->getStatusCode() === 500) {
            return $this->redirect($_COOKIE['failureUrl'] ?? '/');
        } elseif ($responseContent['message'] === 'Invalid request, Order Id already used') {
            return $this->render('main/error_order_id.html.twig', [
                'orderId' => $_COOKIE['orderId'] ?? null,
            ]);
        } elseif ($responseContent['success']) {
            if ($service == 'paypal' || $service == 'perfect_money' || $service == 'visa' || $service == 'mastercard') {
                $redirectUrl = $responseContent['data']['payLink'];
            } elseif ($service == 'orange_money_CM' || $service == 'mtn_mobile_money_CM' || $service == 'express_union') {
                $redirectUrl = $_COOKIE['successUrl'] ?? '/';
            } elseif ($service == 'bitcoin' || $service == 'litecoin' || $service == 'dogecoin') {
                return $this->render('main/crypto.html.twig', [
                    'wallet' => $responseContent['data']['wallet'],
                    'payId' => $responseContent['data']['payId'],
                    'value' => $responseContent['data']['value'],
                    'currency' => $responseContent['data']['currency'],
                ]);
            }
        } else {
            $redirectUrl = $_COOKIE['failureUrl'] ?? '/';
        }

        return $this->redirect($redirectUrl);
    }
}
