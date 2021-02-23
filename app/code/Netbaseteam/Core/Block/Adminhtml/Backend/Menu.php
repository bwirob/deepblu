<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Netbaseteam\Core\Block\Adminhtml\Backend;

use Magento\Backend\Block\AnchorRenderer;
use Magento\Backend\Block\MenuItemChecker;

/**
 * Backend menu block
 *
 * @method $this setAdditionalCacheKeyInfo(array $cacheKeyInfo)
 * @method array getAdditionalCacheKeyInfo()
 * @api
 * @since 100.0.2
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Menu extends \Magento\Backend\Block\Menu
{

    /**
     * @var AnchorRenderer
     */
    private $_anchorRenderer;

    /**
     * @var \Netbaseteam\Marketplace\Helper\Data
     */
    private $_helper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\UrlInterface $url,
        \Magento\Backend\Model\Menu\Filter\IteratorFactory $iteratorFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Backend\Model\Menu\Config $menuConfig,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        array $data = [],
        MenuItemChecker $menuItemChecker = null,
        AnchorRenderer $anchorRenderer = null,
        \Magento\Framework\App\Route\ConfigInterface $routeConfig = null,
        \Magento\Backend\Block\AnchorRenderer $_anchorRenderer,
        \Netbaseteam\Marketplace\Helper\Data $helper
    ) {
        $this->_anchorRenderer = $_anchorRenderer;
        $this->_helper = $helper;
        parent::__construct($context, $url, $iteratorFactory, $authSession, $menuConfig, $localeResolver, $data, $menuItemChecker, $anchorRenderer, $routeConfig);
    }

    /**
     * Render Navigation
     *
     * @param \Magento\Backend\Model\Menu $menu
     * @param int $level
     * @param int $limit
     * @param array $colBrakes
     * @return string HTML
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function renderNavigation($menu, $level = 0, $limit = 0, $colBrakes = [])
    {
        $itemPosition = 1;
        $outputStart = '<ul ' . (0 == $level ? 'id="nav" role="menubar"' : 'role="menu"') . ' >';
        $output = '';

        /** @var $menuItem \Magento\Backend\Model\Menu\Item  */
        foreach ($this->_getMenuIterator($menu) as $menuItem) {
            $menuId = $menuItem->getId();
            $strpos = strpos($menuId, 'Netbaseteam_Marketplace');
            if (!$this->_helper->checkDataEmailToLimitFunction()) {
                if ($strpos !== 0 ) {
                    $itemName = substr($menuId, strrpos($menuId, '::') + 2);
                    $itemClass = str_replace('_', '-', strtolower($itemName));

                    if (is_array($colBrakes)
                        && count($colBrakes)
                        && $colBrakes[$itemPosition]['colbrake']
                        && $itemPosition != 1
                    ) {
                        $output .= '</ul></li><li class="column"><ul role="menu">';
                    }

                    $id = $this->getJsId($menuItem->getId());
                    $subMenu = $this->_addSubMenu($menuItem, $level, $limit, $id);
                    $anchor = $this->_anchorRenderer->renderAnchor($this->getActiveItemModel(), $menuItem, $level);
                    $output .= '<li ' . $this->getUiId($menuItem->getId())
                        . ' class="item-' . $itemClass . ' ' . $this->_renderItemCssClass($menuItem, $level)
                        . ($level == 0 ? '" id="' . $id . '" aria-haspopup="true' : '')
                        . '" role="menu-item">' . $anchor . $subMenu . '</li>';
                    $itemPosition++;
                } else {
                    continue;
                }
            } else {
                $itemName = substr($menuId, strrpos($menuId, '::') + 2);
                $itemClass = str_replace('_', '-', strtolower($itemName));

                if (is_array($colBrakes)
                    && count($colBrakes)
                    && $colBrakes[$itemPosition]['colbrake']
                    && $itemPosition != 1
                ) {
                    $output .= '</ul></li><li class="column"><ul role="menu">';
                }

                $id = $this->getJsId($menuItem->getId());
                $subMenu = $this->_addSubMenu($menuItem, $level, $limit, $id);
                $anchor = $this->_anchorRenderer->renderAnchor($this->getActiveItemModel(), $menuItem, $level);
                $output .= '<li ' . $this->getUiId($menuItem->getId())
                    . ' class="item-' . $itemClass . ' ' . $this->_renderItemCssClass($menuItem, $level)
                    . ($level == 0 ? '" id="' . $id . '" aria-haspopup="true' : '')
                    . '" role="menu-item">' . $anchor . $subMenu . '</li>';
                $itemPosition++;
            }
        }

        if (is_array($colBrakes) && count($colBrakes) && $limit) {
            $output = '<li class="column"><ul role="menu">' . $output . '</ul></li>';
        }

        return $outputStart . $output . '</ul>';
    }
}
