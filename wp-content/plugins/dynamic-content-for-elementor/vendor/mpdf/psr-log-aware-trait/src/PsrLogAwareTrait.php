<?php

namespace DynamicOOOS\Mpdf\PsrLogAwareTrait;

use DynamicOOOS\Psr\Log\LoggerInterface;
trait PsrLogAwareTrait
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
