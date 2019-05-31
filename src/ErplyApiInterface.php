<?php

namespace NewtimeEst\ErplyApi;

interface ErplyApiInterface
{
    /**
     * @property string $request
     * @property array|null $params
     * @return mixed
     */
    public function request($request, $params);
}
