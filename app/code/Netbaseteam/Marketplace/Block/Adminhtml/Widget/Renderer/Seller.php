<?php

namespace Netbaseteam\Marketplace\Block\Adminhtml\Widget\Renderer;

use Magento\Backend\Block\Template;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

class Seller extends Template implements RendererInterface{

    protected $_element;

    protected $_template = 'widget/seller.phtml';


    public function render(AbstractElement $element){
        $this->setElement($element);
        return $this->toHtml();
    }

    public function setElement(AbstractElement $element){
        $this->_element = $element;
    }

    public function getRandom(){
        return $this->mathRandom;
    }

    public function getElement(){
        return $this->_element;
    }

    public function getSellersChooserUrl(){
        return $this->getUrl('marketplace/widget/sellers', array('_current' => true));
    }
}