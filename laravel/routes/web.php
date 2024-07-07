<?php

use App\Http\Controllers\CryptocurrencyController;
use Illuminate\Support\Facades\Route;


Route::get('/', [CryptocurrencyController::class, 'index']);

/* Route::get('/', function (GuzzleClient $guzzleClient, PredisClient $redisClient) {
    // Create a new Guzzle client

    $url = 'https://pro-api.coinmarketcap.com/v1/cryptocurrency/listings/latest';
    $parameters = [
        'start' => '1',
        'limit' => '100',
    ];

    $headers = [
        'Accept' => 'application/json',
        'X-CMC_PRO_API_KEY' => 'c3f841e7-0fff-4d8e-985f-248d29747571'
    ];

    $response = $guzzleClient->request('GET', $url, [
        'headers' => $headers,
        'query' => $parameters
    ]);

    $data = json_decode($response->getBody(), true);
    //dd($data['data']);

    foreach ($data['data'] as $cryptoData) {
        $crypto = new Cryptocurrency(
            $cryptoData['id'],
            $cryptoData['name'],
            $cryptoData['symbol'],
            $cryptoData['quote']['USD']['price'],
            $cryptoData['quote']['USD']['market_cap']
        );
        //Divido gli oggetti in Redis, così ho la possibilità di prendere le singole crypto per ID.
        $redisClient->set("crypto:{$crypto->id}", $crypto->serialize());
        $redisClient->sadd('cryptos', $crypto->id);
        $redisClient->expire("crypto:{$crypto->id}", 3600);
    }

    $redisClient->expire('cryptos', 3600);


    //Prendi tutte le cryptovalute salvate su Redis.

    $cryptoIds = $redisClient->smembers('cryptos');
    $cachedCryptos = [];

    foreach ($cryptoIds as $id) {
        $cachedCryptos[] = Cryptocurrency::deserialize($redisClient->get("crypto:$id"));
    }

    return view('app', ['data' => $data['data']]);
    //return response()->json($data);

});
 */