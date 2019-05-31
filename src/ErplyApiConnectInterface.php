<?php
namespace NewtimeEst\ErplyApi;

interface ErplyApiConnectInterface
{
    /**
     * @property string $request
     * @property array $params
     */
    public function sendRequest($request, $params);
}
