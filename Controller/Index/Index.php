<?php
namespace Maginx\AlsoOrderedProduct\Controller\Index;
class Index extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;
    protected $helper;
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Maginx\AlsoOrderedProduct\Helper\Data $helper)
    {
        $this->helper = $helper;
        $this->resultPageFactory = $resultPageFactory;        
        return parent::__construct($context);
    }
    
    public function execute()
    {
        $this->helper->batchOrderRunAndRecord();
        //$results = $this->helper->getOrderedCollectionFromSku('WT09',2);
        /*foreach($results as $result){
            var_dump($result);
        }*/
        echo "update finish";

        //return $this->resultPageFactory->create();  
    }
}
