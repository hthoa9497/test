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
namespace Bss\MegaMenu\Block\Adminhtml\Category;

use Bss\MegaMenu\Helper\Data;
use Bss\MegaMenu\Model\MenuFactory;
use Bss\MegaMenu\Model\MenuStoresFactory;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\View\Element\Template;

class Tree extends Template
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var MenuFactory
     */
    protected $modelMenuFactory;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var MenuStoresFactory
     */
    protected $menuStoresFactory;

    /**
     * Tree constructor.
     * @param Context $context
     * @param Data $helper
     * @param UrlInterface $urlBuilder
     * @param MenuFactory $modelMenuFactory
     * @param ResourceConnection $resource
     * @param MenuStoresFactory $menuStoresFactory
     */
    public function __construct(
        Context $context,
        Data $helper,
        UrlInterface $urlBuilder,
        MenuFactory $modelMenuFactory,
        ResourceConnection $resource,
        MenuStoresFactory $menuStoresFactory
    ) {
        $this->helper = $helper;
        $this->urlBuilder = $urlBuilder;
        $this->modelMenuFactory = $modelMenuFactory;
        $this->resource = $resource;
        $this->scopeConfig = $context->getScopeConfig();
        $this->menuStoresFactory = $menuStoresFactory;
        parent::__construct($context);
    }

    /**
     * Get helper data
     *
     * @return Data
     */
    public function getHelperData()
    {
        return $this->helper;
    }

    /**
     * Get menu tree
     *
     * @return mixed|string
     */
    public function menuTree()
    {
        $modelId = $this->getRequest()->getParam('id');
        $store = $this->menuStoresFactory->create();
        $storeModel = $store->load($modelId);

        if (!$storeModel) {
            return $this->getDefaultMenu();
        }

        $menu = $storeModel->getValue() ?: $this->getDefaultMenu();

        return $menu;
    }

    /**
     * Get default root menu
     *
     * @return string
     */
    protected function getDefaultMenu()
    {
        return '[{ "text" : "Root Menu", "id" : "root"}]';
    }

    /**
     * Retrieve message text
     *
     * @return string
     */
    public function getText()
    {
        $cacheTypes = 'Page Cache';
        $message = __('One or more of the Cache Types are invalidated: %1. ', $cacheTypes) . ' ';
        $url = $this->urlBuilder->getUrl('adminhtml/cache');
        $message .= __("Please go to <a href='%1'>Cache Management</a> and refresh cache types.", $url);
        return $message;
    }

    /**
     * Get node url
     *
     * @param array|null $type
     * @return string
     */
    public function getNodeUrl($type)
    {
        return $this->urlBuilder->getUrl('megamenu/category/create', ['type' => $type]);
    }

    /**
     * Get menu item url
     *
     * @param array|null $type
     * @return string
     */
    public function getMenuItemUrl($type)
    {
        return $this->urlBuilder->getUrl('megamenu/item/edit', ['type' => $type]);
    }

    /**
     * Get store Id
     *
     * @return int
     */
    public function getStoreId()
    {
        $modelId = $this->getRequest()->getParam('id');
        $store = $this->menuStoresFactory->create();
        $storeModel = $store->load($modelId);
        if (!$storeModel) {
            return '';
        }

        return $storeModel->getStoreId();
    }

    /**
     * Get store Id
     *
     * @return int
     */
    public function getCateId()
    {
        return $this->getRequest()->getParam('id');
    }
}
