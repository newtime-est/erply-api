<?php

namespace NewtimeEst\ErplyApi;

use NewtimeEst\ErplyApi\ErplyApiInterface;
use NewtimeEst\ErplyApi\ErplyApiConnect;

class ErplyApi implements ErplyApiInterface
{
    /**
     * @var string $username
     */
    protected $username;

    /**
     * @var string $password
     */
    protected $password;

    /**
     * @var string $clientCode
     */
    protected $clientCode;

    /**
     * @property string $username
     * @property string $password
     * @property string $clientCode
     */
    public function __construct($username = '', $password = '', $clientCode = '')
    {
        $this->username = $username;
        $this->password = $password;
        $this->clientCode = $clientCode;
    }

    /**
     * @property string $action
     * @property array|null $params
     * @return array
     */
    public function request($action = '', $params = null)
    {
        $connection = new ErplyApiConnect($this->username, $this->password, $this->clientCode);
        return $connection->sendRequest($action, $params);
    }
}
