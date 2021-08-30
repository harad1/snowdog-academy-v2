<?php

namespace Snowdog\Academy\Controller;

use Snowdog\Academy\Model\Cryptocurrency;
use Snowdog\Academy\Model\CryptocurrencyManager;
use Snowdog\Academy\Model\UserCryptocurrencyManager;
use Snowdog\Academy\Model\UserManager;

class Cryptos
{
    private CryptocurrencyManager $cryptocurrencyManager;
    private UserCryptocurrencyManager $userCryptocurrencyManager;
    private UserManager $userManager;
    private Cryptocurrency $cryptocurrency;

    public function __construct(
        CryptocurrencyManager $cryptocurrencyManager,
        UserCryptocurrencyManager $userCryptocurrencyManager,
        UserManager $userManager
    ) {
        $this->cryptocurrencyManager = $cryptocurrencyManager;
        $this->userCryptocurrencyManager = $userCryptocurrencyManager;
        $this->userManager = $userManager;
    }

    public function index(): void
    {
        require __DIR__ . '/../view/cryptos/index.phtml';
    }

    public function buy(string $id): void
    {
        $user = $this->userManager->getByLogin((string) $_SESSION['login']);
        if (!$user) {
            header('Location: /cryptos');
            return;
        }

        $cryptocurrency = $this->cryptocurrencyManager->getCryptocurrencyById($id);
        if (!$cryptocurrency) {
            header('Location: /cryptos');
            return;
        }

        $this->cryptocurrency = $cryptocurrency;

        require __DIR__ . '/../view/cryptos/buy.phtml';
    }

    public function buyPost(string $id): void
    {
        $user = $this->userManager->getByLogin((string) $_SESSION['login']);
        
        if (!$user) {
            header('Location: /cryptos');
            return;
        }

        $cryptocurrency = $this->cryptocurrencyManager->getCryptocurrencyById($id);
        
        if (!$cryptocurrency) {
            header('Location: /cryptos');
            return;
        }
        
        $userId = $user->getId();
        $userFunds = $user->getFunds();

        $amount = $_POST['amount'];
        $cryptocurrencyPrice = $cryptocurrency->getPrice();
        $totalCost = $amount * $cryptocurrencyPrice;
        $missingAmount = $totalCost - $userFunds;

        if ($totalCost > $userFunds){
            $_SESSION['flash'] = "Unfortunately, you do not have enough funds. This transaction requires $$totalCost and you have got $$userFunds. You require a balance of $$missingAmount to complete this transaction.";
        } else {
            $this->userCryptocurrencyManager->addCryptocurrencyToUser($userId,  $id, (int)$amount);
            $this->userCryptocurrencyManager->subtractFoundsFromUser($userId,  $totalCost);
            $_SESSION['flash'] = "You have purchased $amount of $id. Total cost: $$totalCost. Funds left: $$userFunds.";
        }
        header('Location: /cryptos');
    }

    public function sell(string $id): void
    {
        $user = $this->userManager->getByLogin((string) $_SESSION['login']);
        if (!$user) {
            header('Location: /account');
            return;
        }

        $cryptocurrency = $this->cryptocurrencyManager->getCryptocurrencyById($id);
        if (!$cryptocurrency) {
            header('Location: /account');
            return;
        }

        $this->cryptocurrency = $cryptocurrency;

        require __DIR__ . '/../view/cryptos/sell.phtml';
    }

    public function sellPost(string $id): void
    {
        // TODO
        // verify if user is logged in
        // use $this->userCryptocurrencyManager->subtractCryptocurrencyFromUser() method
    }

    public function getCryptocurrencies(): array
    {
        return $this->cryptocurrencyManager->getAllCryptocurrencies();
    }
}