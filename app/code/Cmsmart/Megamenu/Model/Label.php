<?php
namespace Cmsmart\Megamenu\Model;

class Label
{
    const lblNew		= 'new';
    const lblHot		= 'hot';
    const lblSale		= 'sale';

	public function getOptionArray()
    {
        return array(
			''			   => __('-- Please select label type --'),
			''			   => __('Not Set'),
            self::lblNew   => __('New'),
            self::lblHot   => __('Hot'),
            self::lblSale  => __('Sale')
        );
    }
	
	public function getLabel()
    {	
		return [
            ['value' => '', 'label' => __('-- Please select label type --')],
            ['value' => self::lblNew, 'label' => __('New')],
            ['value' => self::lblHot, 'label' => __('Hot')],
            ['value' => self::lblSale, 'label' => __('Sale')],
        ];
    }
}