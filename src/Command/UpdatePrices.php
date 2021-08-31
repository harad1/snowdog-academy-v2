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
        $apiURL = 'https://api.coincap.io/v2/assets';
        $cURL = curl_init();

        curl_setopt($cURL, CURLOPT_URL, $apiURL);
        curl_setopt($cURL, CURLOPT_ENCODING, '');
        curl_setopt($cURL, CURLOPT_HTTPGET, true);
        curl_setopt($cURL, CURLOPT_FAILONERROR, true);
        curl_setopt($cURL, CURLOPT_TIMEOUT, 10);
        curl_setopt($cURL, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json'
        ));

        try {
            $result = curl_exec($cURL);
            $responseRaw = json_decode($result);

            foreach ($responseRaw->data as $cryptoData) {
                $this->cryptocurrencyManager->updatePrice($cryptoData->id, round($cryptoData->priceUsd, 2));
                $this->cryptocurrencyManager->updatePercentageChange($cryptoData->id, round($cryptoData->changePercent24Hr, 2));
            }
            $output->writeln("Success.");

        } catch (Exception $exception) {
            $output->writeln("Error: $exception->getMessage()");

        } finally {
            curl_close($cURL);
            $output->writeln('Closing a cURL session.');
        }
    }
}
