<?php
/**
 * Netbaseteam.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the cmsmart.net license that is
 * available through the world-wide-web at this URL:
 * *
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Netbaseteam
 * @package     Netbaseteam_Extension
 * @copyright   Copyright (c) Cmsmart (http://www.cmsmart.net/)
 *
 */

namespace Netbaseteam\Optimizesite\Observer\Controller;

class FrontSendResponseBefore implements \Magento\Framework\Event\ObserverInterface
{
    public function execute(
        \Magento\Framework\Event\Observer $observer
    )
    {
        $response = $observer->getEvent()->getResponse();
        if (!$response) {
            return;
        }

        $html = $response->getBody();
        if (stripos($html, "</body>") === false) {
            return;
        }

        preg_match_all('~<\s*\bscript\b[^>]*>(.*?)<\s*\/\s*script\s*>~is', $html, $scripts);
        if ($scripts and isset($scripts[0]) and $scripts[0]) {
            $html = preg_replace('~<\s*\bscript\b[^>]*>(.*?)<\s*\/\s*script\s*>~is', '', $response->getBody());
            $scripts = implode("", $scripts[0]);
            $html = str_ireplace("</body>", "$scripts</body>", $html);
            $response->setBody($html);
        }
    }
}
