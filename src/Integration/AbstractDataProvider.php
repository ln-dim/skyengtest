<?php

namespace src\Integration;

abstract class AbstractDataProvider
{
    private $host;
    private $user;
    private $password;

    /**
     * @param $host
     * @param $user
     * @param $password
     */
    public function __construct($host, $user, $password)
    {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     */
    abstract public function get(RequestInterface $request);
}
