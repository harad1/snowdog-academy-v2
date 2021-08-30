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
        $cryptocurrencyName = $cryptocurrency->getName();
        $cryptocurrencySymbol = $cryptocurrency->getSymbol();
        $totalCost = $amount * $cryptocurrencyPrice;
        $missingAmount = $totalCost - $userFunds;

        if ($totalCost > $userFunds){
            $_SESSION['flash'] = "Unfortunately, you do not have enough funds. This transaction requires $$totalCost and you have got $$userFunds. You require an extra $$missingAmount to complete this transaction.";
        } else {
            $this->userCryptocurrencyManager->addCryptocurrencyToUser($userId,  $id, (int)$amount);
            $this->userCryptocurrencyManager->subtractFundsFromUser($userId,  $totalCost);
            $_SESSION['flash'] = "You have purchased $amount of $cryptocurrencyName ($cryptocurrencySymbol). Total cost: $$totalCost.";
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

        $userId = $user->getId();

        $amount = $_POST['amount'];
        $cryptocurrencyPrice = $cryptocurrency->getPrice();
        $cryptocurrencyName = $cryptocurrency->getName();
        $cryptocurrencySymbol = $cryptocurrency->getSymbol();
        $totalCost = $amount * $cryptocurrencyPrice;

        $userCryptocurrencyAmount = $this->userCryptocurrencyManager->getUserCryptocurrency($userId, $id);
        if (!$userCryptocurrencyAmount) {
            header('Location: /account');
            return;
        }

        if ($amount > $userCryptocurrencyAmount->getAmount()){
            $_SESSION['flash'] = "You do not have this much of $cryptocurrencyName ($cryptocurrencySymbol).";
        } else {
            $this->userCryptocurrencyManager->subtractCryptocurrencyFromUser($userId,  $id, (int)$amount);
            $this->userCryptocurrencyManager->addFundsFromUser($userId,  $totalCost);
            $this->userCryptocurrencyManager->removeEmptyCryptoBalance($userId);
            $_SESSION['flash'] = "You have sold $amount of $cryptocurrencyName ($cryptocurrencySymbol).";
        }

        header('Location: /account');
    }

    public function getCryptocurrencies(): array
    {
        return $this->cryptocurrencyManager->getAllCryptocurrencies();
    }
}