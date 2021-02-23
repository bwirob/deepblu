<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Netbaseteam\Productvideo\Model\Source;

class Type implements \Magento\Framework\Option\ArrayInterface
{
    const local		= 'LOCAL';
    const url	= 'URL';
	
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
		return [
            ['value' => '', 'label' => __('PLEASE SELECT')],
            ['value' => self::local, 'label' => __('LOCAL')],
            ['value' => self::url, 'label' => __('URL')],
        ];
	}
}

