<?php
namespace Maginx\AlsoOrderedProduct\Cron;

class Main
{
    protected $_helper;

    public function __construct(
        Helper $helper
    ) {
        $this->_helper = $helper;
    }

    public function execute()
    {

    }

}