<?php

namespace Netbaseteam\Marketplace\Block\Catalog\Product\Widget\Grid\Renderer;

class Thumbnail extends \Netbaseteam\Marketplace\Block\Catalog\Product\Widget\Grid\Renderer\AbstractRenderer
{
    /**
     * @var int
     */
    protected $_defaultWidth = 55;

    /**
     * @var array
     */
    protected $_values;

    /**
     * @var \Magento\Backend\Block\Widget\Grid\Column\Renderer\Options\Converter
     */
    protected $_converter;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Backend\Block\Widget\Grid\Column\Renderer\Options\Converter $converter
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Backend\Block\Widget\Grid\Column\Renderer\Options\Converter $converter,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_converter = $converter;
        $this->_storeManager = $storeManager;
    }

    /**
     * Returns values of the column
     *
     * @return array
     */
    public function getValues()
    {
        if ($this->_values === null) {
            $this->_values = $this->getColumn()->getData('values') ? $this->getColumn()->getData('values') : [];
        }
        return $this->_values;
    }

    /**
     * Prepare data for renderer
     *
     * @return array
     */
    protected function _getValues()
    {
        $values = $this->getColumn()->getValues();
        return $this->_converter->toFlatArray($values);
    }

    /**
     * Renders grid column
     *
     * @param   \Magento\Framework\DataObject $row
     * @return  string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $img;
        $mediaDirectory = $this->_storeManager->getStore()->getBaseUrl(
           \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        ). 'catalog/product';
        if($this->_getValue($row)!=''):
            $imageUrl = $mediaDirectory.$this->_getValue($row);
            $img='<img src="'.$imageUrl.'" width="50" height="50"/>';
        else:
            $img='<img src="'.$this->getViewFileUrl('Magento_Catalog::images/product/placeholder/thumbnail.jpg').'" width="50" height="50" />';
        endif;
        return $img;
    }
}
