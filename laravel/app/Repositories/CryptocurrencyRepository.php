<?php

namespace App\Repositories;

use Predis\Client as PredisClient;
use App\Models\nonEloquent\Cryptocurrency;

class CryptocurrencyRepository
{
    protected $redisClient;

    // Costruttore che inietta la dipendenza di PredisClient
    public function __construct(PredisClient $redisClient)
    {
        $this->redisClient = $redisClient;
    }

    // Salva una criptovaluta nel database Redis
    public function save(Cryptocurrency $crypto)
    {
        $this->redisClient->set("crypto:{$crypto->id}", $crypto->serialize());
        $this->redisClient->sadd('cryptos', $crypto->id);
        $this->redisClient->expire("crypto:{$crypto->id}", 3600);
    }

    // Recupera tutte le criptovalute dal database Redis
    public function getAll()
    {
        $cryptoIds = $this->redisClient->smembers('cryptos');

        if (empty($cryptoIds)) {
            // Se non ci sono dati su Redis, restituisci un array vuoto
            return [];
        }
        
        $cryptos = [];

        foreach ($cryptoIds as $id) {
            $cryptos[] = Cryptocurrency::deserialize($this->redisClient->get("crypto:$id"));
        }

        return $cryptos;
    }

    // Recupera una criptovaluta per ID dal database Redis
    public function getById($id)
    {
        if ($this->redisClient->exists("crypto:$id")) {
            return Cryptocurrency::deserialize($this->redisClient->get("crypto:$id"));
        }

        return null;
    }

    // Pulisce tutte le criptovalute dal database Redis
    public function clearAll()
    {
        $cryptoIds = $this->redisClient->smembers('cryptos');

        foreach ($cryptoIds as $id) {
            $this->redisClient->del("crypto:$id");
        }

        $this->redisClient->del('cryptos');
    }
}
