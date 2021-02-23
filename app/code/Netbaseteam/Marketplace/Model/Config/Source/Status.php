<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Marketplace\Model\Config\Source;

class Status implements \Magento\Framework\Data\OptionSourceInterface
{
    const STATUS_PENDING = 0;
    const STATUS_APPROVE = 1;
    const STATUS_DISAPPROVE = 2;

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        $availableOptions = $this->getAvailableStatuses();
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }

    public function getAvailableStatuses()
    {
        return [self::STATUS_PENDING => __('Pending'), self::STATUS_APPROVE => __('Approved'), self::STATUS_DISAPPROVE => __('Disapproved')];
    }
}
