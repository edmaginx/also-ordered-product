<?php
namespace Maginx\AlsoOrderedProduct\Block;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\View\Element\AbstractBlock;

class Main extends \Magento\Catalog\Block\Product\AbstractProduct implements \Magento\Framework\DataObject\IdentityInterface
{

    protected $_itemCollection;
    protected $_helper;
    protected $_scopeConfig;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Checkout\Model\ResourceModel\Cart $checkoutCart,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Module\Manager $moduleManager,
        \Maginx\AlsoOrderedProduct\Helper\Data $helper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        $this->_checkoutCart = $checkoutCart;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->_checkoutSession = $checkoutSession;
        $this->moduleManager = $moduleManager;
        $this->_helper = $helper;
        $this->_scopeConfig = $scopeConfig;
        parent::__construct(
            $context,
            $data
        );
    }

    protected function _prepareData()
    {
        $product = $this->_coreRegistry->registry('product');
        /* @var $product \Magento\Catalog\Model\Product */

        $productSku = $product->getSku();

        $this->_itemCollection = $this->_helper->getOrderedCollectionFromSku($productSku);
        if (!empty($this->_itemCollection)) {
            //$this->_itemCollection->setPositionOrder()->addStoreFilter();

            if ($this->moduleManager->isEnabled('Magento_Checkout')) {
                $this->_addProductAttributesAndPrices($this->_itemCollection);
            }
            //$this->_itemCollection->addFieldToSelect('*');
            $this->_itemCollection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
            $this->_itemCollection->load();

            foreach ($this->_itemCollection as $product) {
                $product->setDoNotUseCategoryId(true);
            }
        }

        return $this;
    }

    protected function _beforeToHtml()
    {
        $this->_prepareData();
        return parent::_beforeToHtml();
    }

    public function getItems()
    {
        return $this->_itemCollection;
    }

    public function isEnable()
    {
        return $this->_scopeConfig->getValue('maginx/ordered/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getIdentities()
    {
        $identities = [];
        foreach ($this->getItems() as $item) {
            $identities = array_merge($identities, $item->getIdentities());
        }
        return $identities;
    }

    public function canItemsAddToCart()
    {
        foreach ($this->getItems() as $item) {
            if (!$item->isComposite() && $item->isSaleable() && !$item->getRequiredOptions()) {
                return true;
            }
        }
        return false;
    }

}
