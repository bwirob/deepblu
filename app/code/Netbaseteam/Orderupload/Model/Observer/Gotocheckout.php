<?php 
namespace Netbaseteam\Orderupload\Model\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;

class Gotocheckout implements ObserverInterface
{
    protected $_productRepository;
    protected $_productFactory;
    protected $_dataHelper;

    public function __construct(
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\Netbaseteam\Orderupload\Helper\Data $dataHelper,
		\Magento\Catalog\Model\Session $catalogSession
	){
        $this->_productFactory = $productFactory;
        $this->_dataHelper 	   = $dataHelper;
    }
	
	public function _getProductData($sku){
		$product = $this->_productFactory->create();
		return $product->loadByAttribute('sku', $sku);
	}
	
    public function execute(\Magento\Framework\Event\Observer $observer)
    {	
		/* read json file */
		$session_id = session_id();
		
		$quote_item = $observer->getEvent()->getQuoteItem();
		$product_type = $quote_item->getProductType();
		
		$parent_pid = $quote_item->getProductId(); 
		$child_pid = $quote_item->getProductId();
		$child_sku = $quote_item->getSku();
	
		$output_dir = $this->_dataHelper->getBaseDir()."/".$parent_pid."/";

		$jsonFile = $output_dir.$session_id.'.json';
		
		if (file_exists($jsonFile)){
			$str 	= file_get_contents($jsonFile);
			$rows 	= json_decode($str, true);
			
			for($i=0; $i < count($rows); $i++){
				$tmp = array();$tmp1 = array();
				foreach($rows[$i] as $row) {
					/* \zend_debug::dump($row["parent_pid"]); */
					if ($row["parent_pid"] == $parent_pid && $row["child_pid"] == ""){
						$tmp["order_id"] = "";
						$tmp["file"] = $row["file"];
						$tmp["parent_pid"] = $row["parent_pid"];
						$tmp["child_pid"] = $child_pid;
						$tmp["comment"] = $row["comment"];
						$tmp["child_sku"] = $child_sku;
						$tmp1[] = $tmp;
					} else {
						$tmp1[] = $row;
					} 
					$content[]  = $tmp1;
				}
			}
			file_put_contents($jsonFile, json_encode($content));
		}
		
		$quote_item->setSessionFile($parent_pid."/".$session_id);
    }
}
