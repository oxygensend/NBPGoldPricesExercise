<?php

namespace App\Service;

use App\DTO\AvgGoldPrice;
use Psr\Cache\CacheItemInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class NBPSerializer
{
    const URL = 'https://api.nbp.pl/api/cenyzlota';

    public function __construct( readonly private  HttpClientInterface $client,
                                 readonly private   CacheInterface $cache)
    {
    }

    public function serializeGoldPrice(AvgGoldPrice $goldPrice): AvgGoldPrice
    {
        $from = (new \DateTime($goldPrice->getFrom()))->format('Y-m-d');
        $to = (new \DateTime($goldPrice->getTo()))->format('Y-m-d');

        $response = $this->getCachedResponse(self::URL . '/' . $from . '/' . $to);


        $goldPrice->setPrice($this->getAvgGoldPrice($response));
        $goldPrice->setFrom($response[0]['data']);
        $goldPrice->setTo(end($response)['data']);

        return $goldPrice;
    }

    private function getAvgGoldPrice(array $response){
        $prices = array_column($response,'cena');
        $nbOfElements = count($prices);
        $prices = array_filter($prices, fn($el) => $el >= 0 && is_numeric($el));
        return  round(array_sum($prices)/$nbOfElements,2);
    }

    private function getCachedResponse(string $url){

        return  $this->cache->get('nbp_data', function (CacheItemInterface $cacheItem) use ($url) {
            $cacheItem->expiresAfter(5);
            $response = $this->client->request('GET', $url);
            return json_decode($response->getContent(),true);
        });
    }


}