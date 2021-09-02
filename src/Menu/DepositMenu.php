<?php

namespace Snowdog\Academy\Menu;

class DepositMenu extends AbstractMenu
{
    public function getHref(): string
    {
        return '/addFunds';
    }

    public function getLabel(): string
    {
        return 'Deposit';
    }

    public function isVisible(): bool
    {
        if(isset($_SESSION['login'])){
            return true;
        }
        return false;
    }
}