<?php

namespace Snowdog\Academy\Model;

class Cryptocurrency
{
    private string $id;
    private string $symbol;
    private string $name;
    private float $price;
    private float $percent_change;

    public function getId(): string
    {
        return $this->id;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getChange(): float
    {
        return $this->percent_change;
    }

}
