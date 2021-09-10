<?php

namespace Snowdog\Academy\Migration;

use Snowdog\Academy\Core\Database;
use Snowdog\Academy\Model\CryptocurrencyManager;

class Version4
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
        $this->addPercentChangeColumn();
        $this->addPercentChangeCryptoValues();
    }

    private function addPercentChangeColumn(): void
    {
        $updateQuery = <<<SQL
            ALTER TABLE `cryptocurrencies` (
              percent_change decimal(12,2) NOT NULL,
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    SQL;
        $this->database->exec($updateQuery);
    }

    private function addPercentChangeCryptoValues(): void
    {
        $this->cryptocurrencyManager->updatePercentChange('bitcoin', 0.00);
        $this->cryptocurrencyManager->updatePercentChange('ethereum', 0.00);
        $this->cryptocurrencyManager->updatePercentChange('litecoin', 0.00);
        $this->cryptocurrencyManager->updatePercentChange('bitcoin-cash', 0.00);
        $this->cryptocurrencyManager->updatePercentChange('dash', 0.00);
        $this->cryptocurrencyManager->updatePercentChange('ethereum-classic', 0.00);
        $this->cryptocurrencyManager->updatePercentChange('cardano', 0.00);
        $this->cryptocurrencyManager->updatePercentChange('stellar', 0.00);
        $this->cryptocurrencyManager->updatePercentChange('polkadot', 0.00);
        $this->cryptocurrencyManager->updatePercentChange('tron', 0.00);
    }
}
