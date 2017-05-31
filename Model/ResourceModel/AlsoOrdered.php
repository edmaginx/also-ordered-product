<?php
namespace Maginx\AlsoOrderedProduct\Model\ResourceModel;
class AlsoOrdered extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('maginx_alsoorderedproduct_alsoordered', 'maginx_alsoorderedproduct_alsoordered_id');
    }


    public function getRecordIdBySku($sku)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from($this->getMainTable())->where('product_sku = :sku');

        $bind = [':sku' => (string)$sku];

        return $connection->fetchOne($select, $bind);
    }

}
