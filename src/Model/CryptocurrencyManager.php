<?php

namespace Snowdog\Academy\Model;

use Snowdog\Academy\Core\Database;

class CryptocurrencyManager
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function create(string $id, string $symbol, string $name, float $price, float $percent_change): int
    {
        $statement = $this->database->prepare('INSERT INTO cryptocurrencies (id, symbol, name, price, percent_change) VALUES (:id, :symbol, :name, :price, :percent_change)');
        $binds = [
            ':id' => $id,
            ':symbol' => $symbol,
            ':name' => $name,
            ':price' => $price,
            ':percent_change' => $percent_change,
        ];
        $statement->execute($binds);

        return (int) $this->database->lastInsertId();
    }

    public function getCryptocurrencyById(string $id): Cryptocurrency
    {
        $query = $this->database->prepare('SELECT * FROM cryptocurrencies WHERE id = :id');
        $query->setFetchMode(Database::FETCH_CLASS, Cryptocurrency::class);
        $query->execute([':id' => $id]);

        return $query->fetch(Database::FETCH_CLASS);
    }

    public function getAllCryptocurrencies(): array
    {
        $query = $this->database->query('SELECT * FROM cryptocurrencies');

        return $query->fetchAll(Database::FETCH_CLASS, Cryptocurrency::class);
    }

    public function updatePrice(string $id, float $price): void
    {
        $statement = $this->database->prepare('
                                        UPDATE cryptocurrencies
                                        SET price = :price
                                        WHERE id = :id
                                    ');
        $binds = [
            ':id' => $id,
            ':price' => $price,
        ];
        $statement->execute($binds);
    }

    public function updatePercentChange(string $id, float $percent_change): void
    {
       $statement = $this->database->prepare('
                                             UPDATE cryptocurrencies
                                             SET `percent_change` = :percent_change
                                             WHERE id = :id');
             $binds = [
                 ':id' => $id,
                 ':percent_change' => $percent_change,
             ];
             $statement->execute($binds);
    }
}
