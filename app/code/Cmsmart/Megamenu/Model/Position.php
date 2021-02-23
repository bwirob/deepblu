<?php
namespace Cmsmart\Megamenu\Model;

class Position
{
	const top_menu		= 'top';
    const left_menu		= 'left';
    const both		= 'both';
 
	public function getOptionArray()
    {
        return array(
			""				 => __('-- Please select --'),
            self::top_menu   => __('Top'),
            self::left_menu  => __('Left'),
            self::both 		 => __('Both (Top & Left)')
        );
    }
	
    public function getPosition()
    {	
		return [
            ['value' => '', 'label' => __('-- Please select position --')],
            ['value' => self::top_menu, 'label' => __('Top')],
            ['value' => self::left_menu, 'label' => __('Left')],
            ['value' => self::both, 'label' => __('Both (Top & Left)')],
        ];
    }
}