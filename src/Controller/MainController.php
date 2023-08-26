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
        $amount =  $request->request->get('amount');
        $currency = $request->request->get('currency');
        $description = $request->request->get('description');
        $orderId = $request->request->get('orderId');
        $apiKey = $request->request->get('apiKey');
        $service = $request->request->get('service');
        $successUrl = $request->request->get('successUrl');
        $failureUrl = $request->request->get('failureUrl');

        setcookie('amount', $amount);
        setcookie('currency', $currency);
        setcookie('description', $description);
        setcookie('orderId', $orderId);
        setcookie('apiKey', $apiKey);
        setcookie('service', $service);
        setcookie('successUrl', $successUrl);
        setcookie('failureUrl', $failureUrl);

        return $this->render('main/index.html.twig', [
            'amount' => $amount,
            'description' => $description,
            'orderId' => $orderId,
            'currency' => $currency,
            'service' => $service
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
                ],
            ]);
            
        }

        $responseContent = json_decode($response->getContent(), true);

        if ($responseContent['success']) {
            $redirectUrl = $_COOKIE['successUrl'] ?? '/';
        } else {
            $redirectUrl = $_COOKIE['failureUrl'] ?? '/';
        }


        return $this->redirect($redirectUrl);
        
    }
}
