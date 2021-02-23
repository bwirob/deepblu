<?php
namespace Netbaseteam\Locator\Block;

use Magento\Store\Model\ScopeInterface;


abstract class Abstractlocaltor extends \Magento\Framework\View\Element\Template
{

    protected $_localtorFactory;
    protected $_coreRegistry;
    protected $_dataHelper;
    protected $_countryFactory;
    protected $_workdateFactory;
    protected $_scheduleFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Netbaseteam\Locator\Model\LocatorFactory $localtorFactory,
        \Netbaseteam\Locator\Helper\Data $dataHelper,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\App\ResponseInterface $responInterface,
        \Netbaseteam\Locator\Model\WorkdateFactory $workdateFactory,
        \Netbaseteam\Locator\Model\ScheduleFactory $scheduleFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_dataHelper = $dataHelper;
        $this->_coreRegistry = $coreRegistry;
        $this->_localtorFactory = $localtorFactory;
        $this->_countryFactory = $countryFactory;
        $this->_workdateFactory = $workdateFactory;
        $this->_jsonHelper = $jsonHelper;
        $this->_response = $responInterface;
        $this->_scheduleFactory = $scheduleFactory;
    }


    public function getStoreId(){

        return  $this->_dataHelper->getStoreviewId();
    }

   

}
