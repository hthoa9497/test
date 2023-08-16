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
namespace Bss\MegaMenu\Controller\Adminhtml\Category;

use Bss\MegaMenu\Model\MenuStores;
use Bss\MegaMenu\Model\MenuStoresFactory;
use Bss\MegaMenu\Model\ResourceModel\MenuStores\CollectionFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Store\Model\ScopeInterface;

class Create extends \Magento\Backend\App\Action
{

    /**
     * @var \Bss\Megamenu\Model\MenuItemsFactory
     */
    protected $modelMenuFactory;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $config;

    /**
     * @var MenuStoresFactory
     */
    protected $menuStoresFactory;

    /**
     * Create constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Bss\MegaMenu\Model\MenuItemsFactory $modelMenuFactory
     * @param JsonFactory $resultJsonFactory
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $config
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param CollectionFactory $menuStoreCollectionFactory
     * @param MenuStores $menuStores
     * @param MenuStoresFactory $menuStoresFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Bss\MegaMenu\Model\MenuItemsFactory $modelMenuFactory,
        JsonFactory $resultJsonFactory,
        \Magento\Framework\App\Config\Storage\WriterInterface $config,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        MenuStoresFactory $menuStoresFactory
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->storeManager = $storeManager;
        $this->modelMenuFactory = $modelMenuFactory;
        $this->menuStoresFactory = $menuStoresFactory;
    }

    /**
     * Create new menu category
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $resultEcho = $this->resultJsonFactory->create();
        $params = $this->getRequest()->getParams();
        $menu = $this->modelMenuFactory->create();
        $menu = $menu->getCollection()
            ->addFieldToSelect('*');
        $menuId = $menu->getData('menu_id');

        if ($menuId) {
            $menuId = $menuId[0]['menu_id'];
        } else {
            $menuId = '';
        }

        $cateId = $params['cateId'];

        if ($params['menu']) {
            $store = $this->menuStoresFactory->create();
            $storeModel = $store->load($cateId);
            $storeModel->setData('value', $params['menu']);
            $storeModel->save();
        }

        $result['empty'] = true;
        $result['mega_menu_id'] = $menuId;

        if ($params['type'] == 'delete') {
            $id = explode('_', $params['node_id'] ?? '');
            $menuCollection = $this->modelMenuFactory->create();
            $menuCollection = $menuCollection->getCollection()
                ->addFieldToSelect('*')
                ->addFieldToFilter('menu_id', $id[1])
                ->addFieldToFilter('store_id', $params['store_id'])
                ->getLastItem();

            $menuCollection->delete();
        }

        return $resultEcho->setData($result);
    }
}
