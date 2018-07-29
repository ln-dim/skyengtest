<?php

namespace src\Decorator;

use Exception;
use Psr\Log\LoggerInterface;
use src\Integration\AbstractDataProvider;
use src\Integration\RequestInterface;
use src\Integration\ResponseInterface;

class LoggerDecorator extends AbstractDecorator
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * LoggerDecorator constructor.
     * @param AbstractDataProvider $dataProvider
     * @param LoggerInterface $logger
     */
    public function __construct(AbstractDataProvider $dataProvider, LoggerInterface $logger)
    {
        parent::__construct($dataProvider);
        $this->logger = $logger;
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function get(RequestInterface $request)
    {
        try {
            return parent::get($request);
        } catch (Exception $e) {
            $this->logger->critical('Error occurred while getting data from external service.', [
                'exception' => $e,
                'request' => $request,
                'dataProvider' => $this->dataProvider,
            ]);
        }
        return null;
    }
}