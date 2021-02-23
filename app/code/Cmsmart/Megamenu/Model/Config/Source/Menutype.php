<?php

namespace Cmsmart\Megamenu\Model\Config\Source;

class Menutype implements \Magento\Framework\Option\ArrayInterface
{
    const HORIZONTAL	= 1;
    const VERTICAL		= 2;
    const BOTH			= 3;

    public function getOptionArray()
    {
        return array(
            self::HORIZONTAL    => __('Horizontal'),
            self::VERTICAL   => __('Vertical'),
            self::BOTH   => __('Both (Horizontal & Vertical)')
        );
    }
	
	public function toOptionArray()
    {
        return array(
            self::HORIZONTAL => __('Horizontal'),
            self::VERTICAL   => __('Vertical'),
            self::BOTH   	 => __('Both (Horizontal & Vertical)')
        );
    }
}