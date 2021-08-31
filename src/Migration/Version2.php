<?php

namespace Snowdog\Academy\Migration;

use Snowdog\Academy\Core\Database;
use Snowdog\Academy\Model\CryptocurrencyManager;

class Version2
{
    private Database $database;
    private CryptocurrencyManager $cryptocurrencyManager;

    public function __construct(Database $database, CryptocurrencyManager $cryptocurrencyManager)
    {
        $this->database = $database;
        $this->cryptocurrencyManager = $cryptocurrencyManager;
    }

    public function __invoke()
    {
        $this->createCryptocurrenciesTable();
        $this->addCryptocurrencies();
    }

    private function createCryptocurrenciesTable(): void
    {
        $createQuery = <<<SQL
            CREATE TABLE `cryptocurrencies` (
              `id` varchar(255) NOT NULL,
              `symbol` varchar(20) NOT NULL,
              `name` varchar(255) NOT NULL,
              `price` decimal(12,2) NOT NULL,
              `change` decimal(12,2) NOT NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY `symbol` (`symbol`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    SQL;
        $this->database->exec($createQuery);
    }

    private function addCryptocurrencies(): void
    {
        $this->cryptocurrencyManager->create('bitcoin', 'BTC', 'Bitcoin', 40000);
        $this->cryptocurrencyManager->create('ethereum', 'ETH', 'Ethereum', 2500);
        $this->cryptocurrencyManager->create('litecoin', 'LTC', 'Litecoin', 174);
        $this->cryptocurrencyManager->create('bitcoin-cash', 'BCH', 'Bitcoin Cash', 624);
        $this->cryptocurrencyManager->create('dash', 'DASH', 'Dash', 205);
        $this->cryptocurrencyManager->create('ethereum-classic', 'ETC', 'Ethereum Classic', 58);
        $this->cryptocurrencyManager->create('cardano', 'ADA', 'Cardano', 1.5);
        $this->cryptocurrencyManager->create('stellar', 'XLM', 'Stellar', 0.3);
        $this->cryptocurrencyManager->create('polkadot', 'DOT', 'Polkadot', 24);
        $this->cryptocurrencyManager->create('tron', 'TRX', 'Tron', 0.07);
    }
}
