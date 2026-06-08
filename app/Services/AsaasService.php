<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class AsaasService
{
    private $apiKey;
    private $apiUrl;

    public function __construct()
    {
        $this->apiKey = env('ASAAS_API_KEY');
        // Usar sandbox como padrão para desenvolvimento, se não estiver definido
        $this->apiUrl = env('ASAAS_API_URL', 'https://sandbox.asaas.com/api/v3');
    }

    /**
     * Retorna os headers de autenticação padrão para a API do Asaas.
     */
    private function getHeaders()
    {
        return [
            'access_token' => $this->apiKey,
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Cria ou recupera o Customer no Asaas para o usuário especificado.
     */
    public function getOrCreateCustomer(User $user, $cpfCnpj = null)
    {
        if ($user->asaas_customer_id) {
            return $user->asaas_customer_id;
        }

        try {
            $response = Http::withHeaders($this->getHeaders())
                ->post($this->apiUrl . '/customers', [
                    'name' => $user->name,
                    'email' => $user->email,
                    'cpfCnpj' => $cpfCnpj,
                    'externalReference' => $user->id,
                ]);

            if ($response->successful()) {
                $customerId = $response->json('id');
                $user->update(['asaas_customer_id' => $customerId]);
                return $customerId;
            }

            Log::error('Erro ao criar Customer no Asaas', [
                'user_id' => $user->id,
                'status' => $response->status(),
                'response' => $response->json()
            ]);
            
            return null;
        } catch (\Exception $e) {
            Log::error('Exceção ao criar Customer no Asaas: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Cria uma assinatura (Subscription) para o usuário.
     */
    public function createSubscription(User $user, $planType)
    {
        // Garante que o cliente existe
        $customerId = $this->getOrCreateCustomer($user);
        
        if (!$customerId) {
            return ['success' => false, 'message' => 'Não foi possível criar o cliente no Asaas.'];
        }

        // Define o valor com base no plano (Mínimo de R$ 5,00 exigido pelo Checkout do Asaas)
        $value = $planType === 'max' ? 9.99 : 5.00;
        
        $payload = [
            'customer' => $customerId,
            'billingType' => 'UNDEFINED', // O usuário escolhe no checkout do Asaas
            'nextDueDate' => now()->format('Y-m-d'), // Cobrança imediata
            'value' => $value,
            'cycle' => 'MONTHLY',
            'description' => 'Assinatura AjudaPet - Plano ' . strtoupper($planType),
        ];

        try {
            $response = Http::withHeaders($this->getHeaders())
                ->post($this->apiUrl . '/subscriptions', $payload);

            if ($response->successful()) {
                $subscriptionId = $response->json('id');
                
                // Atualiza o banco de dados
                $user->update([
                    'asaas_subscription_id' => $subscriptionId,
                    'plan_type' => $planType,
                    'subscription_status' => 'PENDING', // PENDENTE ATÉ O PAGAMENTO CAIR
                ]);

                // Busca a primeira cobrança (fatura) gerada para pegar o Link de Pagamento
                $paymentsResponse = Http::withHeaders($this->getHeaders())
                    ->get($this->apiUrl . '/subscriptions/' . $subscriptionId . '/payments', [
                        'status' => 'PENDING',
                        'limit' => 1
                    ]);

                $invoiceUrl = null;
                if ($paymentsResponse->successful() && !empty($paymentsResponse->json('data'))) {
                    $invoiceUrl = $paymentsResponse->json('data')[0]['invoiceUrl'];
                }

                if ($invoiceUrl) {
                    return ['success' => true, 'invoiceUrl' => $invoiceUrl];
                } else {
                    return ['success' => false, 'message' => 'Assinatura criada, mas não foi possível gerar o link de pagamento.'];
                }
            }

            Log::error('Erro ao criar Subscription no Asaas', [
                'user_id' => $user->id,
                'status' => $response->status(),
                'response' => $response->json()
            ]);
            
            return ['success' => false, 'message' => 'Erro na API do Asaas.'];
        } catch (\Exception $e) {
            Log::error('Exceção ao criar Subscription no Asaas: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Busca os dados de uma assinatura específica.
     */
    public function getSubscription($subscriptionId)
    {
        try {
            $response = Http::withHeaders($this->getHeaders())
                ->get($this->apiUrl . '/subscriptions/' . $subscriptionId);

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }
            return ['success' => false];
        } catch (\Exception $e) {
            return ['success' => false];
        }
    }

    /**
     * Busca o QR Code PIX da primeira cobrança pendente de uma assinatura.
     */
    public function getSubscriptionPixQrCode($subscriptionId)
    {
        try {
            // Pega os pagamentos pendentes da assinatura
            $paymentsResponse = Http::withHeaders($this->getHeaders())
                ->get($this->apiUrl . '/subscriptions/' . $subscriptionId . '/payments', [
                    'status' => 'PENDING',
                    'limit' => 1
                ]);

            if ($paymentsResponse->successful() && !empty($paymentsResponse->json('data'))) {
                $paymentId = $paymentsResponse->json('data')[0]['id'];

                // Com o ID da cobrança, pegamos o QR Code
                $qrCodeResponse = Http::withHeaders($this->getHeaders())
                    ->get($this->apiUrl . '/payments/' . $paymentId . '/pixQrCode');

                if ($qrCodeResponse->successful()) {
                    return [
                        'success' => true,
                        'payload' => $qrCodeResponse->json('payload'),
                        'encodedImage' => $qrCodeResponse->json('encodedImage')
                    ];
                }
            }

            Log::error('Erro ao buscar QR Code PIX no Asaas', [
                'subscriptionId' => $subscriptionId,
                'response' => $paymentsResponse->json() ?? 'Sem resposta'
            ]);
            return ['success' => false, 'message' => 'QR Code não disponível.'];
        } catch (\Exception $e) {
            Log::error('Exceção ao buscar QR Code PIX: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
