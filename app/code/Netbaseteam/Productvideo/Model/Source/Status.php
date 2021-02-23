<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Netbaseteam\Productvideo\Model\Source;

class Status implements \Magento\Framework\Option\ArrayInterface
{
	const active		= 2;
    const pending		= 1;
    const disable		= 0;
	
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
		return [
            ['value' => self::active, 'label' => __('Active')],
            ['value' => self::pending, 'label' => __('Pending')],
            ['value' => self::disable, 'label' => __('Disable')],
        ];
	}
}

