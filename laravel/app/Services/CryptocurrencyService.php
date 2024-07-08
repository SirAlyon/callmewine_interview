<?php

namespace App\Services;

use GuzzleHttp\Client as GuzzleClient;
use App\Repositories\CryptocurrencyRepository;
use App\Models\nonEloquent\Cryptocurrency;
use Illuminate\Support\Facades\Log;

class CryptocurrencyService
{
    protected $guzzleClient;
    protected $cryptoRepository;

    // Costruttore che inietta le dipendenze di GuzzleClient e CryptocurrencyRepository
    public function __construct(GuzzleClient $guzzleClient, CryptocurrencyRepository $cryptoRepository)
    {
        $this->guzzleClient = $guzzleClient;
        $this->cryptoRepository = $cryptoRepository;
    }

    // Metodo per recuperare e memorizzare le criptovalute
    public function fetchAndStoreCryptocurrencies()
    {
        $url = 'https://pro-api.coinmarketcap.com/v1/cryptocurrency/listings/latest';
        $parameters = [
            'start' => '1', // Inizia dalla prima criptovaluta
            'limit' => '100', // Limita il risultato a 100 criptovalute
            'convert' => 'USD' // Converti i prezzi in USD
        ];

        $headers = [
            'Accept' => 'application/json',
            'X-CMC_PRO_API_KEY' => env('CMC_API_KEY') // Usa l'API key dal file .env
        ];

        try {
            // Richiesta HTTP per ottenere i dati delle criptovalute
            $response = $this->guzzleClient->request('GET', $url, [
                'headers' => $headers,
                'query' => $parameters
            ]);

            // Decodifica la risposta JSON in un array associativo
            $data = json_decode($response->getBody(), true);
            
            
            // Pulisci redis prima di salvare i nuovi dati
            $this->cryptoRepository->clearAll();
             // Itera attraverso i dati delle criptovalute e crea istanze di Cryptocurrency
            foreach ($data['data'] as $cryptoData) {
                $crypto = new Cryptocurrency(
                    $cryptoData['id'],
                    $cryptoData['cmc_rank'],
                    $cryptoData['name'],
                    $cryptoData['symbol'],
                    $cryptoData['quote']['USD']['price'],
                    $cryptoData['quote']['USD']['market_cap']
                );

                // Salva ogni criptovaluta 
                $this->cryptoRepository->save($crypto);
            }

            return $this->transformCryptoData($data['data']);
        } catch (\Exception $e) {
            return null; // Se l'API fallisce, restituisci null
        }
    }
    
    // Metodo per ottenere tutte le criptovalute dal repository
    public function getAllCryptocurrencies()
    {
        $cryptos = $this->cryptoRepository->getAll();
        return array_map(function($crypto) {
            return $crypto->toArray();
        }, $cryptos);
    }

    // Metodo per ottenere una singola criptovaluta per ID (non utilizzato)
    public function getCryptocurrencyById($id)
    {
        $crypto = $this->cryptoRepository->getById($id);
        return $crypto ? $crypto->toArray() : null;
    }

    // Metodo privato per trasformare i dati delle criptovalute in un array strutturato
    private function transformCryptoData($cryptoDataArray)
    {
        return array_map(function($cryptoData) {
            return [
                'id' => $cryptoData['id'],
                'rank' => $cryptoData['cmc_rank'],
                'name' => $cryptoData['name'],
                'symbol' => $cryptoData['symbol'],
                'price' => $cryptoData['quote']['USD']['price'],
                'market_cap' => $cryptoData['quote']['USD']['market_cap'],
            ];
        }, $cryptoDataArray);
    }
}
