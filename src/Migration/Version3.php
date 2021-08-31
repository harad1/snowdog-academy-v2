<?php

namespace Snowdog\Academy\Migration;

use Snowdog\Academy\Core\Database;
use Snowdog\Academy\Model\CryptocurrencyManager;

class Version3
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
        $this->createUserCryptocurrenciesTable();
    }

    private function createUserCryptocurrenciesTable(): void
    {
        $createQuery = <<<SQL
CREATE TABLE `user_cryptocurrencies` (
  `user_id` int(11) unsigned NOT NULL,
  `cryptocurrency_id` varchar(20) NOT NULL,
  `amount` decimal(10,2) unsigned NOT NULL,
  PRIMARY KEY (`user_id`, `cryptocurrency_id`),
  CONSTRAINT user_cryptocurrencies_user_id_cryptocurrency_id_unique_index UNIQUE (`user_id`, `cryptocurrency_id`),
  CONSTRAINT user_cryptocurrencies_users_user_id_fk FOREIGN KEY (`user_id`) REFERENCES users (`id`),
  CONSTRAINT user_cryptocurrencies_cryptocurrencies_cryptocurrency_id_fk FOREIGN KEY (`cryptocurrency_id`) REFERENCES cryptocurrencies (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;
        $this->database->exec($createQuery);
    }
}
