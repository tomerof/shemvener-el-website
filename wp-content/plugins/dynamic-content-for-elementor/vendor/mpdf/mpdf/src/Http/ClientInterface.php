<?php

namespace DynamicOOOS\Mpdf\Http;

use DynamicOOOS\Psr\Http\Message\RequestInterface;
interface ClientInterface
{
    public function sendRequest(RequestInterface $request);
}
