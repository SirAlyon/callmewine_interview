<?php

namespace App\Http\Controllers;

use App\Services\CryptocurrencyService;
use Illuminate\Http\Request;

class CryptocurrencyController extends Controller
{
    protected $cryptoService;

    public function __construct(CryptocurrencyService $cryptoService)
    {
        $this->cryptoService = $cryptoService;
    }

    public function index()
    {
        $cryptos = $this->cryptoService->fetchAndStoreCryptocurrencies();

        if ($cryptos === null) {
            // Se l'API fallisce, prendi i dati da Redis
            $cryptos = $this->cryptoService->getAllCryptocurrencies();
            $message = 'Showing cached data from Redis due to API failure.';
        } else {
            $message = 'Showing fresh data from API.';
        }
        //dd($message);
        return view('app', ['data' => $cryptos, 'message' => $message]);
    }
}
