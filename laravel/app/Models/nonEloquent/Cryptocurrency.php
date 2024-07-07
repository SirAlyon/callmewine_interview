<?php

namespace App\Models\nonEloquent;


class Cryptocurrency
{
    public $id;
    public $rank;
    public $name;
    public $symbol;
    public $price;
    public $market_cap;


    public function __construct($id,$rank, $name, $symbol, $price, $market_cap)
    {
        $this->id = $id;
        $this->rank = $rank;
        $this->name = $name;
        $this->symbol = $symbol;
        $this->price = $price;
        $this->market_cap = $market_cap;

    }

    public function serialize()
    {
        return json_encode([
            'id' => $this->id,
            'rank' => $this->rank,
            'name' => $this->name,
            'symbol' => $this->symbol,
            'price' => $this->price,
            'market_cap' => $this->market_cap
        ]);
    }

    public static function deserialize($data)
    {
        $decoded = json_decode($data, true);
        return new self(
            $decoded['id'],
            $decoded['rank'],
            $decoded['name'],
            $decoded['symbol'],
            $decoded['price'],
            $decoded['market_cap']

        );
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'rank' => $this->rank,
            'name' => $this->name,
            'symbol' => $this->symbol,
            'price' => $this->price,
            'market_cap' => $this->market_cap,
        ];
    }
}