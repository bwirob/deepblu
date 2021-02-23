<?php

namespace Netbaseteam\Marketplace\Block\Catalog\Product\Widget\Grid\Filter;

class AbstractFilter extends \Magento\Framework\View\Element\AbstractBlock
{
    /**
     * Column related to filter
     *
     * @var \Netbaseteam\Marketplace\Block\Catalog\Product\Widget\Grid\Column
     */
    protected $_column;

    /**
     * @var \Magento\Framework\DB\Helper
     */
    protected $_resourceHelper;

    /**
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Framework\DB\Helper $resourceHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Framework\DB\Helper $resourceHelper,
        array $data = []
    ) {
        $this->_resourceHelper = $resourceHelper;
        parent::__construct($context, $data);
    }

    /**
     * Set column related to filter
     *
     * @param \Netbaseteam\Marketplace\Block\Catalog\Product\Widget\Grid\Column $column
     * @return \Netbaseteam\Marketplace\Block\Catalog\Product\Widget\Grid\Filter\AbstractFilter
     */
    public function setColumn($column)
    {
        $this->_column = $column;
        return $this;
    }

    /**
     * Retrieve column related to filter
     *
     * @return \Netbaseteam\Marketplace\Block\Catalog\Product\Widget\Grid\Column
     */
    public function getColumn()
    {
        return $this->_column;
    }

    /**
     * Retrieve html name of filter
     *
     * @return string
     */
    protected function _getHtmlName()
    {
        return $this->escapeHtml($this->getColumn()->getId());
    }

    /**
     * Retrieve html id of filter
     *
     * @return string
     */
    protected function _getHtmlId()
    {
        return $this->escapeHtml($this->getColumn()->getHtmlId());
    }

    /**
     * Retrieve escaped value
     *
     * @param mixed $index
     * @return string
     */
    public function getEscapedValue($index = null)
    {
        return $this->escapeHtml((string)$this->getValue($index));
    }

    /**
     * Retrieve condition
     *
     * @return array
     */
    public function getCondition()
    {
        $likeExpression = $this->_resourceHelper->addLikeEscape($this->getValue(), ['position' => 'any']);
        return ['like' => $likeExpression];
    }

    /**
     * Retrieve filter html
     *
     * @return string
     */
    public function getHtml()
    {
        return '';
    }
}
