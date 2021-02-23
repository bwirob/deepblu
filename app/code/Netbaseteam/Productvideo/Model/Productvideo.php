<?php

namespace Netbaseteam\Productvideo\Model;

/**
 * Productvideo Model
 *
 * @method \Netbaseteam\Productvideo\Model\Resource\Page _getResource()
 * @method \Netbaseteam\Productvideo\Model\Resource\Page getResource()
 */
class Productvideo extends \Magento\Framework\Model\AbstractModel
{
	const local			= 'unknown';
    const youtube		= 'youtube';
    const vimeo			= 'vimeo';
    const dailymotion	= 'dailymotion';
    const twitch		= 'twitch';
	
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Netbaseteam\Productvideo\Model\ResourceModel\Productvideo');
    }

}
