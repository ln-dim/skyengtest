<?php

namespace src\Decorator;

use src\Integration\AbstractDataProvider;
use src\Integration\RequestInterface;
use src\Integration\ResponseInterface;

abstract class AbstractDecorator extends AbstractDataProvider
{
    /**
     * @var AbstractDataProvider
     */
    protected $dataProvider;

    /**
     * Decorator constructor.
     * @param AbstractDataProvider $dataProvider
     */
    public function __construct(AbstractDataProvider $dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function get(RequestInterface $request)
    {
        return $this->dataProvider->get($request);
    }
}