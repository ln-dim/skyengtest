<?php

namespace src\Decorator;

use DateTime;
use Psr\Cache\CacheException;
use Psr\Log\LoggerInterface;
use src\Integration\AbstractDataProvider;
use Psr\Cache\CacheItemPoolInterface;
use src\Integration\RequestInterface;

class CacheDecorator extends AbstractDecorator
{
    /**
     * @var CacheItemPoolInterface
     */
    protected $cache;
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * CacheDecorator constructor.
     * @param AbstractDataProvider $dataProvider
     * @param CacheItemPoolInterface $cache
     */
    public function __construct(AbstractDataProvider $dataProvider, CacheItemPoolInterface $cache, LoggerInterface $logger)
    {
        parent::__construct($dataProvider);
        $this->cache = $cache;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function get(RequestInterface $request)
    {
        try {
            $cacheKey = $this->getCacheKey($request);
            $cacheItem = $this->cache->getItem($cacheKey);
            if ($cacheItem->isHit()) {
                return $cacheItem->get();
            }
        } catch (CacheException $e) {
            $this->logger->warning('An error occurred while getting data from cache.', [
                'exception' => $e,
                'request' => $request,
                'dataProvider' => $this->dataProvider,
            ]);
        }

        $response = parent::get($request);

        try {
            $cacheItem
                ->set($response)
                ->expiresAt(
                    (new DateTime())->modify('+1 day')
                );
        } catch (CacheException $e) {
            $this->logger->warning('An error occurred while trying to put data into cache.', [
                'exception' => $e,
                'request' => $request,
                'response' => $response,
                'dataProvider' => $this->dataProvider,
            ]);
        }

        return $response;
    }

    /**
     * @param RequestInterface $request
     * @return string
     */
    protected function getCacheKey(RequestInterface $request)
    {
        return get_class($this->dataProvider) . '_' . json_encode($request);
    }
}