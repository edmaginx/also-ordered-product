<?php
namespace Maginx\AlsoOrderedProduct\Model;

class AlsoOrdered extends \Magento\Framework\Model\AbstractModel implements \Maginx\AlsoOrderedProduct\Api\Data\AlsoOrderedInterface, \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'maginx_alsoorderedproduct_alsoordered';

    protected function _construct()
    {
        $this->_init('Maginx\AlsoOrderedProduct\Model\ResourceModel\AlsoOrdered');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getRecordIdBySku($sku)
    {
        return $this->_getResource()->getRecordIdBySku($sku);
    }
}
