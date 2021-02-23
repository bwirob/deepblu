<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Marketplace\Model\Config\Source\Vacation;

class Productstatus implements \Magento\Framework\Data\OptionSourceInterface
{
    /**#@+
     * Vacation's Statuses
     */
    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 0;

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options[] = ['label' => '', 'value' => ''];
        $availableOptions = $this->getAvailableStatuses();
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }

    /**
     * Prepare grid's statuses.
     * Available event vacation_grid_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLE => __('Enable'), self::STATUS_DISABLE => __('Disable')];
    }
}
