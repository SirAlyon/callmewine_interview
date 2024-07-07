<?php

namespace App\Repositories;

use Predis\Client as PredisClient;
use App\Models\nonEloquent\Cryptocurrency;

class CryptocurrencyRepository
{
    protected $redisClient;

    public function __construct(PredisClient $redisClient)
    {
        $this->redisClient = $redisClient;
    }

    public function save(Cryptocurrency $crypto)
    {
        $this->redisClient->set("crypto:{$crypto->id}", $crypto->serialize());
        $this->redisClient->sadd('cryptos', $crypto->id);
        $this->redisClient->expire("crypto:{$crypto->id}", 3600);
    }

    public function getAll()
    {
        $cryptoIds = $this->redisClient->smembers('cryptos');
        $cryptos = [];

        foreach ($cryptoIds as $id) {
            $cryptos[] = Cryptocurrency::deserialize($this->redisClient->get("crypto:$id"));
        }

        return $cryptos;
    }

    public function getById($id)
    {
        if ($this->redisClient->exists("crypto:$id")) {
            return Cryptocurrency::deserialize($this->redisClient->get("crypto:$id"));
        }

        return null;
    }

    public function clearAll()
    {
        $cryptoIds = $this->redisClient->smembers('cryptos');

        foreach ($cryptoIds as $id) {
            $this->redisClient->del("crypto:$id");
        }

        $this->redisClient->del('cryptos');
    }
}
