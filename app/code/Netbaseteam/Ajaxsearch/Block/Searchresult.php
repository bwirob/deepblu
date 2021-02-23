<?php
namespace Netbaseteam\Ajaxsearch\Block;

class Searchresult extends \Magento\Framework\View\Element\Template
{

    protected $_coreRegistry = null;

   
    protected $_helper;
    
    protected $_categoryFactory;
    
    public function __construct(
        \Magento\Framework\View\ Element\Template\Context $context,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Http\Context $httpContext,
        \Netbaseteam\Ajaxsearch\Helper\Data $helper
    
    ) {
        $this->_coreRegistry = $registry;
        $this->httpContext = $httpContext;
        $this->_helper = $helper;
        $this->_categoryFactory = $categoryFactory;
        parent::__construct($context);
    }

    public function getCateResult(){

        $resultTotal = array();

            $q = $this->getRequest()->getParam('q');;
            $cateCollection = $this->_categoryFactory->create()->getCollection()
                    ->addAttributeToSelect('Id')
                    ->addAttributeToSelect('name')
                    ->addAttributeToSelect('url')

                    ->addFieldToFilter('is_active', array('eq'=>'1'))
                    ->addAttributeToFilter(                        
                                    array(
                                        array('attribute'=>'name','like' => '%'.$q.'%')                            
                                    )
                                );
            foreach ($cateCollection as $cate) {
                
                $resultTotal[$cate->getID()]['name'] = $cate->getName();
                $resultTotal[$cate->getID()]['level'] = $cate->getLevel();
                $resultTotal[$cate->getID()]['url'] = $cate->getUrl();
                $resultTotal[$cate->getID()]['parent_cate'] = $cate->getParentCategory()->getName();        
              }

        

        return $resultTotal;

    }


}
