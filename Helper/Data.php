<?php
namespace Maginx\AlsoOrderedProduct\Helper;

use Maginx\AlsoOrderedProduct\Model\AlsoOrdered;

class Data
{
    protected $_alsoOrderedFactory;
    protected $_alsoOrdered;
    protected $_alsoOrderedRepository;
    protected $_scopeConfig;
    protected $_resourceConfig;
    protected $_orderModel;
    protected $_productCollectionFactory;

    public function __construct(
        \Maginx\AlsoOrderedProduct\Model\AlsoOrdered $alsoOrdered,
        \Maginx\AlsoOrderedProduct\Model\AlsoOrderedRepository $alsoOrderedRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Config\Model\ResourceModel\Config $resourceConfig,
        \Magento\Sales\Model\Order $orderModel,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
    ) {
        $this->_alsoOrdered = $alsoOrdered;
        $this->_alsoOrderedRepository = $alsoOrderedRepository;
        $this->_scopeConfig = $scopeConfig;
        $this->_resourceConfig = $resourceConfig;
        $this->_orderModel = $orderModel;
        $this->_productCollectionFactory = $productCollectionFactory;
    }

    public function batchOrderRunAndRecord()
    {
        $lastOrderIdObject = $this->_scopeConfig->getValue(
            'maginx/ordered/lastest_order_id',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (empty($lastOrderIdObject)) {
            $this->_resourceConfig->saveConfig(
                'maginx/ordered/lastest_order_id',
                0,
                'default',
                0
            );
            $lastOrderIdObject = 0;
        }

        $lastOrderId = $lastOrderIdObject;

        $orders = $this->_orderModel->getCollection()->addFieldToFilter('entity_id', ['gt'=>$lastOrderId]);

        $lastId = $lastOrderId;
        foreach ($orders as $order) {
            $items = $order->getAllVisibleItems();

            //load product collection by ids to get all parent products
            $alsoOrderedPidFilter = [];
            foreach ($items as $item) {
                $alsoOrderedPidFilter[] = ['eq'=>$item->getProductId()];
            }
            $collection = $this->_productCollectionFactory->create();
            $collection->addFieldToFilter('entity_id', $alsoOrderedPidFilter);

            $this->updateSkuRecordFromOrder($collection);
            $lastId = $order->getId();
        }
        if ($lastId > $lastOrderId) {
            $this->_resourceConfig->saveConfig(
                'maginx/ordered/lastest_order_id',
                $lastId,
                'default',
                0
            );
        }
    }

    public function updateSkuRecordFromOrder($itemsArray)
    {
        $skuArray = [];
        foreach ($itemsArray as $item) {
            $skuArray[] = $item->getData('sku');
        }
        foreach ($skuArray as $sku) {
            $tempOtherSkuArray = $skuArray;
            if (($key = array_search($sku, $skuArray)) !== false) {
                unset($tempOtherSkuArray[$key]);
            }
            $modelRepo = $this->_alsoOrderedRepository;
            $model  = $this->_alsoOrdered;
            $recordId = $model->getRecordIdBySku($sku);

            if ($recordId) {
                //road record
                $skuAlsoOrderedInfo = $modelRepo->getById($recordId);
                $alsoOrderedRecord = $skuAlsoOrderedInfo->getData('also_ordered_record');
                $updatedRecord = $this->handleRecord($alsoOrderedRecord, $tempOtherSkuArray);
                $skuAlsoOrderedInfo->setData('also_ordered_record', $updatedRecord);
                $modelRepo->save($skuAlsoOrderedInfo);
            } else {
                //create record
                $skuAlsoOrderedInfo = $modelRepo->create();
                $skuAlsoOrderedInfo->setData('product_sku', $sku);
                $modelRepo->save($skuAlsoOrderedInfo);
                $initArray = [];
                foreach ($tempOtherSkuArray as $temp) {
                    $initArray[$temp] = 1;
                }
                $skuAlsoOrderedInfo->setData('also_ordered_record', json_encode($initArray));
                $modelRepo->save($skuAlsoOrderedInfo);
            }
        }
    }

    public function handleRecord($alsoOrderedRecord, $skuArray)
    {
        $recordArray = json_decode($alsoOrderedRecord, true);
        foreach ($skuArray as $sku) {
            if (array_key_exists($sku, $recordArray)) {
                $recordArray[$sku] = $recordArray[$sku] + 1;
            } else {
                $recordArray[$sku] = 1;
            }
        }
        return json_encode($recordArray);
    }

    public function getOrderedCollectionFromSku($sku, $limit = 4)
    {
        $modelRepo = $this->_alsoOrderedRepository;
        $model  = $this->_alsoOrdered;
        $recordId = $model->getRecordIdBySku($sku);
        if ($recordId) {
            $skuAlsoOrderedInfo = $modelRepo->getById($recordId);
            $alsoOrderedRecord = $skuAlsoOrderedInfo->getData('also_ordered_record');
            $recordArray = json_decode($alsoOrderedRecord, true);
            $isSuccess = arsort($recordArray);
            if ($isSuccess) {
                $newRecordArray = array_slice($recordArray, 0, $limit);
            }
            $mostOrderedSkusFilter = [];
            foreach ($newRecordArray as $sku => $count) {
                $mostOrderedSkusFilter[] = ['eq'=>$sku];
            }
            if (!empty($mostOrderedSkusFilter)) {
                $collection = $this->_productCollectionFactory->create();
                $collection->addFieldToFilter('sku', $mostOrderedSkusFilter);
                $collection->setPageSize($limit); // fetching only 4 products by default
                return $collection;
            }
        }
        return [];
    }
}
