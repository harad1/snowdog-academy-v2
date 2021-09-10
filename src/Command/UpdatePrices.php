<?php

namespace Snowdog\Academy\Command;

use Exception;
use Snowdog\Academy\Core\Migration;
use Snowdog\Academy\Model\CryptocurrencyManager;
use Symfony\Component\Console\Output\OutputInterface;

class UpdatePrices
{
    private CryptocurrencyManager $cryptocurrencyManager;

    public function __construct(CryptocurrencyManager $cryptocurrencyManager)
    {
        $this->cryptocurrencyManager = $cryptocurrencyManager;
    }

    public function __invoke(OutputInterface $output)
    {
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.coinstats.app/public/v1/coins?skip=0&limit=100',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $response = curl_exec($curl);
    $responseRaw = json_decode($response, True);

    for ($i=0; $i<count($responseRaw['coins']); $i++) {
        $cryptoId = $responseRaw['coins'][$i]['id'];
        $cryptoPrice = round($responseRaw['coins'][$i]['price'], 2);
        $cryptoPriceChange = round($responseRaw['coins'][$i]['priceChange1d'], 2);
        $this->cryptocurrencyManager->updatePrice($cryptoId, $cryptoPrice);
        $this->cryptocurrencyManager->updatePercentChange($cryptoId, $cryptoPriceChange);

    };

    curl_close($curl);

    }
}
