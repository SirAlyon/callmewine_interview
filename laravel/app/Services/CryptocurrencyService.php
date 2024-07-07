<?php

namespace App\Services;

use GuzzleHttp\Client as GuzzleClient;
use App\Repositories\CryptocurrencyRepository;
use App\Models\nonEloquent\Cryptocurrency;

class CryptocurrencyService
{
    protected $guzzleClient;
    protected $cryptoRepository;

    public function __construct(GuzzleClient $guzzleClient, CryptocurrencyRepository $cryptoRepository)
    {
        $this->guzzleClient = $guzzleClient;
        $this->cryptoRepository = $cryptoRepository;
    }

    public function fetchAndStoreCryptocurrencies()
    {
        $url = 'https://pro-api.coinmarketcap.com/v1/cryptocurrency/listings/latest';
        $parameters = [
            'start' => '1',
            'limit' => '100',
            'convert' => 'USD'
        ];

        $headers = [
            'Accept' => 'application/json',
            'X-CMC_PRO_API_KEY' => env('CMC_API_KEY') // Usa l'API key dal file .env
        ];

        try {
            $response = $this->guzzleClient->request('GET', $url, [
                'headers' => $headers,
                'query' => $parameters
            ]);

            $data = json_decode($response->getBody(), true);

            $this->cryptoRepository->clearAll();
            
            foreach ($data['data'] as $cryptoData) {
                $crypto = new Cryptocurrency(
                    $cryptoData['id'],
                    $cryptoData['cmc_rank'],
                    $cryptoData['name'],
                    $cryptoData['symbol'],
                    $cryptoData['quote']['USD']['price'],
                    $cryptoData['quote']['USD']['market_cap']
                );

                $this->cryptoRepository->save($crypto);
            }

            return array_map(function($cryptoData) {
                return [
                    'id' => $cryptoData['id'],
                    'rank' => $cryptoData['rank'],
                    'name' => $cryptoData['name'],
                    'symbol' => $cryptoData['symbol'],
                    'price' => $cryptoData['quote']['USD']['price'],
                    'marketCap' => $cryptoData['quote']['USD']['market_cap'],
                ];
            }, $data['data']);
        } catch (\Exception $e) {
            return null; // Se l'API fallisce, restituisci null
        }
    }

    public function getAllCryptocurrencies()
    {
        $cryptos = $this->cryptoRepository->getAll();
        return array_map(function($crypto) {
            return $crypto->toArray();
        }, $cryptos);
    }

    public function getCryptocurrencyById($id)
    {
        $crypto = $this->cryptoRepository->getById($id);
        return $crypto ? $crypto->toArray() : null;
    }
}
