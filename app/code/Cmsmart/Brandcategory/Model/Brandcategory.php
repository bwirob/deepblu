<?php

namespace Cmsmart\Brandcategory\Model;

/**
 * Brandcategory Model
 *
 * @method \Cmsmart\Brandcategory\Model\Resource\Page _getResource()
 * @method \Cmsmart\Brandcategory\Model\Resource\Page getResource()
 */
class Brandcategory extends \Magento\Framework\Model\AbstractModel
{
	/**
	 * Brand's Statuses
	 */
	const STATUS_ENABLED = 1;
	const STATUS_DISABLED = 0;
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Cmsmart\Brandcategory\Model\ResourceModel\Brandcategory');
    }

	/**
     * Prepare page's statuses.
     * Available event cms_page_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }
	
	public function getStatusArray()
    {
        return array(
            self::STATUS_ENABLED   => __('Enabled'),
            self::STATUS_DISABLED  => __('Disabled')
        );
    }
}
