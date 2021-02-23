<?php

namespace Netbaseteam\Marketplace\Controller\Adminhtml\Widget;

class Sellers extends \Magento\Widget\Controller\Adminhtml\Widget\Instance
{
    /**
     * @var \Magento\Framework\View\Layout
     */
    protected $layout;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Widget\Model\Widget\InstanceFactory $widgetFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Math\Random $mathRandom
     * @param \Magento\Framework\Translate\InlineInterface $translateInline
     * @param \Magento\Framework\View\Layout $layout
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Widget\Model\Widget\InstanceFactory $widgetFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Math\Random $mathRandom,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        \Magento\Framework\View\Layout $layout
    ) {
        $this->layout = $layout;
        parent::__construct($context, $coreRegistry, $widgetFactory, $logger, $mathRandom, $translateInline);
    }

    /**
     * Categories chooser Action (Ajax request)
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        $selected = $this->getRequest()->getParam('selected', '');
        $uniqID = $this->getRequest()->getParam('uniq_id', '');

        $chooser = $this->layout->createBlock('Netbaseteam\Marketplace\Block\Adminhtml\Widget\Seller\Chooser')
            ->setUseMassaction(true)
            ->setSelectedSellers(explode(',', $selected))
            ->setTemplate('widget/seller/grid/extended.phtml')
        ;

        if(!empty($uniqID)) $chooser->setId($uniqID);

            /* @var $serializer Mage_Adminhtml_Block_Widget_Grid_Serializer */
        $serializer = $this->layout->createBlock('Magento\Backend\Block\Widget\Grid\Serializer');
        $serializer->setGridBlock($chooser)
                    ->setInputElementName('selected_sellers')
                    ->setReloadParamName('selected_sellers')
                    ->setSerializeData($chooser->getSelectedSellers());
        $this->setBody($chooser->toHtml().$serializer->toHtml());

        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_RAW);
        return $resultRaw->setContents($chooser->toHtml().$serializer->toHtml());
    }

}