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

use Bss\MegaMenu\Model\MenuFactory;
use Bss\MegaMenu\Model\MenuStoresFactory;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\UrlInterface;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Indexer\Category\Flat\State;
use Magento\Cms\Model\ResourceModel\Block\CollectionFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\View\Element\Template;

class Content extends Template
{
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
     * @var CollectionFactory
     */
    protected $blockColFactory;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var State
     */
    protected $categoryFlatConfig;

    /**
     * @var MenuStoresFactory
     */
    protected $menuStoresFactory;

    /**
     * @var \Bss\MegaMenu\Helper\Data
     */
    protected $helper;

    /**
     * Content constructor.
     *
     * @param Context $context
     * @param UrlInterface $urlBuilder
     * @param MenuFactory $modelMenuFactory
     * @param CollectionFactory $blockColFactory
     * @param ResourceConnection $resource
     * @param State $categoryFlatState
     * @param CategoryFactory $categoryFactory
     * @param MenuStoresFactory $menuStoresFactory
     * @param \Bss\MegaMenu\Helper\Data $helper
     */
    public function __construct(
        Context $context,
        UrlInterface $urlBuilder,
        MenuFactory $modelMenuFactory,
        CollectionFactory $blockColFactory,
        ResourceConnection $resource,
        State $categoryFlatState,
        CategoryFactory $categoryFactory,
        MenuStoresFactory $menuStoresFactory,
        \Bss\MegaMenu\Helper\Data $helper
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->modelMenuFactory = $modelMenuFactory;
        $this->resource = $resource;
        $this->blockColFactory = $blockColFactory;
        $this->categoryFactory = $categoryFactory;
        $this->categoryFlatConfig = $categoryFlatState;
        $this->menuStoresFactory = $menuStoresFactory;
        $this->helper = $helper;
        parent::__construct($context);
    }

    /**
     * @return false|string
     */
    public function getBlockCollection()
    {
        $blocks = $this->blockColFactory->create();
        return $this->helper->serialize($blocks->getData());
    }

    /**
     * @return false|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCategoryCollection()
    {
        $storeId = $this->getStoreId();
        $category = $this->categoryFactory->create();
        $storeId = is_numeric($storeId) ? $storeId : explode(",", $storeId);
        if ($storeId == null || $storeId == 0) {
            $rootCat = $this->_storeManager->getStore()->getRootCategoryId();
        } else {
            $rootCat = $this->_storeManager->getStore($storeId[0])->getRootCategoryId();
        }
        $category->load($rootCat);
        $data = $this->getChildCategories($category);
        return $this->helper->serialize($data);
    }

    /**
     * @param $category
     * @param array $data
     * @param int $level
     * @return array
     */
    public function getChildCategories($category, $data = [], $level = 0)
    {
        if ($level != 0) {
            $data[] = [
                'id' => $category->getId(),
                'title' => str_repeat('-', $level - 1) . ' ' . $category->getName()
            ];
        }

        if ($category->hasChildren()) {
            $childCategories = $category->getChildrenCategories();
            $level++;
            foreach ($childCategories as $childCategory) {
                $data = $this->getChildCategories($childCategory, $data, $level);
            }
        }
        return $data;
    }

    /**
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
     * @return string
     */
    public function getStoreName()
    {
        $modelId = $this->getRequest()->getParam('id');
        $store = $this->menuStoresFactory->create();
        $storeModel = $store->load($modelId);
        if (!$storeModel) {
            return '';
        }

        return $storeModel->getName();
    }

    /**
     * @return mixed
     */
    public function getCategoryStoreId()
    {
        return $this->getRequest()->getParam('id');
    }
}
