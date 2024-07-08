<?php

namespace App\Http\Controllers;

use App\Services\CryptocurrencyService;
use Illuminate\Http\Request;

class CryptocurrencyController extends Controller
{
    protected $cryptoService;

    // Costruttore che inietta il servizio delle criptovalute
    public function __construct(CryptocurrencyService $cryptoService)
    {
        $this->cryptoService = $cryptoService;
    }

    public function index()
    {
        //dd($this->cryptoService->fetchAndStoreCryptocurrencies());
        $cryptos = $this->cryptoService->fetchAndStoreCryptocurrencies();

        if ($cryptos === null) {
            // Se l'API fallisce, prendi i dati da Redis
            $cryptos = $this->cryptoService->getAllCryptocurrencies();

            if (empty($cryptos)) {
                // Se non ci sono dati su Redis, mostra un messaggio di errore
                $alert['message'] = $this->getNoDataMessage();
                $alert['color'] = 'red';

                return view('index', ['data' => [], 'alert' => $alert]);
            }
            //API fallita, uso i dati di redis
            $alert['message'] = $this->getApiErrorMessage();
            $alert['color'] = 'yellow';
        } else {
            //Ho i dati dall'API
            $alert['message'] = $this->getSuccessMessage();
            $alert['color'] = 'green';
        }

        return view('index', ['data' => $cryptos,'alert' => $alert]);
    }

    // Metodo privato per ottenere il messaggio di successo
    private function getSuccessMessage()
    {
        return 'Showing fresh data from API.';
    }

    // Metodo privato per ottenere il messaggio di errore
    private function getApiErrorMessage()
    {
        return 'Showing cached data from Redis due to API failure.';
    }

    // Metodo privato per ottenere il messaggio di assenza di dati
    private function getNoDataMessage()
    {
        return 'No data available from API or cache.';
    }
}
