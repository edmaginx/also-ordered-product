<?php
namespace Maginx\AlsoOrderedProduct\Model\ResourceModel\AlsoOrdered;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Maginx\AlsoOrderedProduct\Model\AlsoOrdered', 'Maginx\AlsoOrderedProduct\Model\ResourceModel\AlsoOrdered');
    }
}
