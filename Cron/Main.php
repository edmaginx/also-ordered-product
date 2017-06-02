<?php
namespace Maginx\AlsoOrderedProduct\Cron;

class Main
{
    protected $_helper;

    public function __construct(
        \Maginx\AlsoOrderedProduct\Helper\Data $helper,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->_helper = $helper;
        $this->_logger = $logger;
    }

    public function execute()
    {
        $this->_helper->batchOrderRunAndRecord();
    }
}
