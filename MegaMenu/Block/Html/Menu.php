<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_MegaMenu
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\MegaMenu\Block\Html;

use Magento\Cms\Block\Block;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;

class Menu extends Template
{
    /**
     * @var \Bss\MegaMenu\Helper\Data
     */
    protected $helper;

    /**
     * @var \Bss\MegaMenu\Model\Menu
     */
    protected $menu;

    /**
     * @var \Bss\MegaMenu\Model\ResourceModel\MenuItems\CollectionFactory
     */
    protected $menuItemsCollection;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Theme\Block\Html\Topmenu
     */
    protected $topMenuDefault;

    /**
     * Default store view id
     */
    const DEFAULT_STOREVIEW = '0';

    /**
     * Menu constructor.
     * @param Template\Context $context
     * @param \Bss\MegaMenu\Helper\Data $helper
     * @param \Bss\MegaMenu\Model\Menu $menu
     * @param \Bss\MegaMenu\Model\ResourceModel\MenuItems\CollectionFactory $menuItemsCollection
     * @param \Magento\Theme\Block\Html\Topmenu $topMenuDefault
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param array $data
     */
    public function __construct(
        Template\Context                                              $context,
        \Bss\MegaMenu\Helper\Data                                     $helper,
        \Bss\MegaMenu\Model\Menu                                      $menu,
        \Bss\MegaMenu\Model\ResourceModel\MenuItems\CollectionFactory $menuItemsCollection,
        \Magento\Theme\Block\Html\Topmenu                             $topMenuDefault,
        \Magento\Framework\App\ResourceConnection                     $resource,
        array                                                         $data = []
    ) {
        parent::__construct($context, $data);
        $this->helper = $helper;
        $this->topMenuDefault = $topMenuDefault;
        $this->menu = $menu;
        $this->storeManager = $context->getStoreManager();
        $this->menuItemsCollection = $menuItemsCollection;
        $this->resource = $resource;
        $this->scopeConfig = $context->getScopeConfig();
    }

    /**
     * Get current store id
     *
     * @return int
     * @throws NoSuchEntityException
     */
    protected function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * Get module helper
     *
     * @return \Bss\MegaMenu\Helper\Data
     */
    public function getHelperData()
    {
        return $this->helper;
    }

    /**
     * Get default top menu
     *
     * @return \Magento\Theme\Block\Html\Topmenu
     */
    public function getTopMenuDefault()
    {
        return $this->topMenuDefault;
    }

    /**
     * Get menu HTML
     *
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getHtml()
    {
        $storeId = $this->getStoreId();
        $collection = $this->menuItemsCollection->create();
        $collection->addFieldToFilter('status', 1);
        // check match id store_id like 1,2,3,4
        $collection->addFieldToFilter('store_id', [
            ['like' => $storeId . ',%'],
            ['like' => '%,' . $storeId . ',%'],
            ['like' => '%,' . $storeId],
            ['eq' => $storeId],
            ['eq' => self::DEFAULT_STOREVIEW]
        ]);
        $collectionData = [];
        if ($collection->getData()) {
            $values = array_column($collection->getData(), 'priority');
            $maxPriority = max($values);
            foreach ($values as $key => $value) {
                if ($maxPriority > 0 && $value == 0) {
                    unset($values[$key]);
                }
            }
            $minPriority = (int)min($values);
            foreach ($collection->getData() as $value) {
                if ($value['priority'] == $minPriority) {
                    $collectionData[] = $value;
                }
            }
        }
        $menu = null;
        $new_arr = [];
        foreach ($collectionData as $arr) {
            $new_arr['j1_' . $arr['menu_id']] = $arr;
        }
        if ($this->getHelperData()->getMegaMenuConfig($storeId)) {
            $menu = $this->helper->unserialize(
                $this->getHelperData()->getMegaMenuConfig($storeId)
            );
            if (!$menu) {
                $menu = $this->getHelperData()->getMegaMenuConfig() ? $this->helper->unserialize($this->getHelperData()->getMegaMenuConfig()) : '';
            }
        } else {
            $menu = $this->getHelperData()->getMegaMenuConfig() ? $this->helper->unserialize($this->getHelperData()->getMegaMenuConfig()) : '';
        }
        if (isset($menu[0])) {
            $menu = $menu[0];
        }
        if (!isset($menu['children']) || empty($new_arr)) {
            return '';
        }
        $html = $this->_getHtml($menu['children'], $new_arr);
        return $html;
    }

    /**
     * Get menu HTML content
     *
     * @param array $menus
     * @param array $collection
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _getHtml($menus, $collection)
    {
        $html = '';
        $childrenText = '';
        $i = 1;
        foreach ($menus as $menu) {
            if (!array_key_exists($menu['id'], $collection)) {
                continue;
            }
            $menu2 = $collection[$menu['id']];

            if (isset($menu['children'][0])) {
                $childrenText = 'parent';
            }

            $checkIsFullWith = 0;

            if ($menu2['type'] == 2 || $menu2['type'] == 3) {
                $checkIsFullWith = 1;
            }

            //get content of mega menu
            $menu_content = isset($menu2['content']) ? $this->helper->unserialize($menu2['content']) : '';

            $html .= '<li class="level0 dropdown '
                . ($checkIsFullWith == 1 ? 'bss-megamenu-fw ' : '')
                . 'level-top ' . $childrenText . ' ui-menu-item">
                    <a class="level-top ui-corner-all '
                . (isset($menu_content['custom_css']) ? $menu_content['custom_css'] : '')
                . '" href="'
                . $this->helper->getLinkUrl($menu2)
                . '" ><span>'
                . __($menu['text']);
            $html = $this->getLabelColor($menu2, $html);

            $html .= '</span></a>';

            $this->_getContentHtml($menu, 0, $i, $collection, $menu2['type'], $html);

            $html .= '</li>';
            $i++;
        }
        return $html;
    }

    /**
     * Get content html
     *
     * @param array $menu
     * @param int $level
     * @param int $nav
     * @param object $collection
     * @param array $type
     * @param string $html
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function _getContentHtml($menu, $level, $nav, $collection, $type, &$html)
    {
        switch ($type) {
            case 1:
                $html .= $this->_getChildHtmlDefault($menu, $level, $nav, $collection);
                break;

            case 2:
                $html .= $this->_getChildHtmlCatagoryList($menu, $collection);
                break;

            case 3:
                $html .= $this->_getChildHtmlContent($menu, $collection);
                break;
        }
        return $html;
    }

    /**
     * @param object $menu
     * @param int $level
     * @param int $nav
     * @param array $collection
     * @return string
     * @throws NoSuchEntityException
     */
    protected function _getChildHtmlDefault($menu, $level, $nav, $collection)
    {
        $html = '';
        if (isset($menu['children']) && count($menu['children']) == 0) {
            return $html;
        }

        $countCollection = $this->countCollection($menu, $collection);

        if ($countCollection == 0) {
            return $html;
        }

        $html .= '<ul
            class="dropdown-menu fullwidth level0 submenu ui-menu ui-widget ui-widget-content ui-corner-all"
            role="menu">';
        $i = 1;
        $level++;
        foreach ($menu['children'] as $children) {
            if (!array_key_exists($children['id'], $collection)) {
                continue;
            }

            $menu2 = $collection[$children['id']];
            $menu_content = isset($menu2['content']) ? $this->helper->unserialize($menu2['content']) : '';
            $html .= '<li class="dropdown-submenu level1 nav-4-1 first ui-menu-item">
                    <a class="ui-corner-all '
                . (isset($menu_content['custom_css']) ? $menu_content['custom_css'] : '') . '"
                        href="' . $this->helper->getLinkUrl($menu2) . '"><span>' . __($children['text']) . '</span>';
            $html = $this->getLabelColor($menu2, $html);
            $html .= '</a>';
            $nav_child = $nav . '-' . $i;
            if (isset($children['children'][0])) {
                $html .= $this->_getChildHtmlDefault($children, $level, $nav_child, $collection);
            }
            $html .= '</li>';
            $i++;
        }
        $html .= '</ul>';
        return $html;
    }

    /**
     * @param object $menu
     * @param object $collection
     * @return string
     * @throws LocalizedException
     */
    protected function _getChildHtmlCatagoryList($menu, $collection)
    {
        $html = '';

        $countCollection = $this->countCollection($menu, $collection);

        $menu2 = $collection[$menu['id']];

        if ($countCollection == 0
            && $menu2['block_top'] == ''
            && $menu2['block_left'] == ''
            && $menu2['block_bottom'] == ''
            && $menu2['block_right'] == ''
        ) {
            return $html;
        }

        $html .= '<ul class="dropdown-menu fullwidth"><li class="bss-megamenu-content withdesc">';

        if ($menu2['block_top'] != '') {
            $html .= '<div class="row clearfix">';
            $html .= $this->getLayout()
                ->createBlock(Block::class)
                ->setBlockId($menu2['block_top'])
                ->toHtml();
            $html .= '</div><hr>';
        }

        $html .= '<div class="row clearfix">';

        $size = $this->helper->checkSize($menu2);

        $html = $this->getHtmlLeft($menu2, $html, $size);

        $html .= '<div class="col-sm-' . $size . '">';

        $html = $this->_getChildHtmlCatagoryListSecond($menu, $collection, $html, $menu2, $size);

        $html = $this->getHtmlBottom($menu2, $html);

        $html .= '</li></ul>';
        return $html;
    }

    /**
     * @param object $menu
     * @param array $collection
     * @param string $html
     * @param array $menu2
     * @param int $size
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _getChildHtmlCatagoryListSecond($menu, $collection, $html, $menu2, $size)
    {
        foreach ($menu['children'] as $children) {
            if (!array_key_exists($children['id'], $collection)) {
                continue;
            }
            //get content of mega menu
            $obj = $collection[$children['id']];

            $menu_content = isset($obj['content']) ? $this->helper->unserialize($obj['content']) : '';

            $html .= '<div class="col-sm-12">
                         <h3 class="title">
                            <a href="' . $this->helper->getLinkUrl($collection[$children['id']]) . '"' .
                (isset($menu_content['custom_css']) ? ' class="' . $menu_content['custom_css'] . '" ' : '')
                . '>' . __($children['text']);
            $html = $this->getLabelColor($obj, $html);
            $html .= '</a></h3>';

            if (isset($children['children'][0])) {
                $html .= '<ul>';
                foreach ($children['children'] as $child) {
                    if (!array_key_exists($child['id'], $collection)) {
                        continue;
                    }
                    //get content of mega menu
                    $obj = $collection[$child['id']];
                    $menu_content = isset($obj['content']) ? $this->helper->unserialize($obj['content']) : '';
                    $html .= '<li><a href="' . $this->helper->getLinkUrl($collection[$child['id']]) . '"'
                        . (isset($menu_content['custom_css']) ? ' class="' . $menu_content['custom_css'] . '" ' : '')
                        . '><span>' . __($child['text']) . '</span>';
                    $html = $this->getLabelColor($obj, $html);
                    $html .= '</a></li>';
                }
                $html .= '</ul>';
            }

            $html .= '</div>';
        }
        $html .= '</div>';

        if ($menu2['block_right'] != '') {
            $html .= '<div class="col-sm-' . $size . '">';
            $html .= $this->getLayout()->createBlock(Block::class)
                ->setBlockId($menu2['block_right'])->toHtml();
            $html .= '</div>';
        }

        $html .= '</div>';
        return $html;
    }

    /**
     * @param array $menu
     * @param array $collection
     * @return string
     * @throws LocalizedException
     */
    protected function _getChildHtmlContent($menu, $collection)
    {
        $html = '';

        $menu2 = $collection[$menu['id']];

        if ($menu2['block_top'] == ''
            && $menu2['block_left'] == ''
            && $menu2['block_bottom'] == ''
            && $menu2['block_right'] == ''
            && $menu2['block_content'] == ''
        ) {
            return $html;
        }

        $html .= '<ul class="dropdown-menu fullwidth"><li class="bss-megamenu-content">';

        if ($menu2['block_top'] != '') {
            $html .= '<div class="row clearfix">';
            $html .= $this->getLayout()
                ->createBlock(Block::class)
                ->setBlockId($menu2['block_top'])
                ->toHtml();
            $html .= '</div><hr>';
        }

        $html .= '<div class="row clearfix">';

        $size = $this->helper->checkSize($menu2);

        $html = $this->getHtmlLeft($menu2, $html, $size);

        $html .= '<div class="col-sm-' . $size . '">';
        $html .= $this->getLayout()
            ->createBlock(Block::class)
            ->setBlockId($menu2['block_content'])
            ->toHtml();
        $html .= '</div>';

        if ($menu2['block_right'] != '') {
            $html .= '<div class="col-sm-' . $size . '">';
            $html .= $this->getLayout()
                ->createBlock(Block::class)
                ->setBlockId($menu2['block_right'])
                ->toHtml();
            $html .= '</div>';
        }

        $html .= '</div>';

        $html = $this->getHtmlBottom($menu2, $html);

        $html .= '</li></ul>';
        return $html;
    }

    /**
     * @param array $menu2
     * @param string $html
     * @param int $size
     * @return string
     * @throws LocalizedException
     */
    protected function getHtmlLeft($menu2, $html, $size)
    {
        if ($menu2['block_left'] != '') {
            $html .= '<div class="col-sm-' . $size . '">';
            $html .= $this->getLayout()
                ->createBlock(Block::class)
                ->setBlockId($menu2['block_left'])
                ->toHtml();
            $html .= '</div>';
        }
        return $html;
    }

    /**
     * @param array $menu2
     * @param string $html
     * @return string
     * @throws LocalizedException
     */
    protected function getHtmlBottom($menu2, $html)
    {
        if ($menu2['block_bottom'] != '') {
            $html .= '<hr><div class="row cleafix">';
            $html .= $this->getLayout()
                ->createBlock(Block::class)
                ->setBlockId($menu2['block_bottom'])
                ->toHtml();
            $html .= '</div>';
        }
        return $html;
    }

    /**
     * @param array $menu2
     * @param string $html
     * @return string
     */
    protected function getLabelColor($menu2, $html)
    {
        if ($menu2['label'] != '') {
            $html .= $this->helper->getLabelColor($menu2['label']);
        }
        return $html;
    }

    /**
     * @param object $menu
     * @param array $collection
     * @return mixed
     */
    protected function countCollection($menu, $collection)
    {
        $countCollection = 0;
        foreach ($menu['children'] as $children) {
            if (array_key_exists($children['id'], $collection)) {
                $countCollection++;
            }
        }
        return $countCollection;
    }
}
