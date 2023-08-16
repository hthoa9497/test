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
use Bss\MegaMenu\Model\ResourceModel\MenuItems\CollectionFactory;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\Registry;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var MenuStores
     */
    protected $menuStores;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Backend session
     *
     * @var \Magento\Backend\Model\Session
     */
    protected $backendSession;

    /**
     * Result forward factory
     *
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var TypeListInterface
     */
    protected $cacheTypeList;

    /**
     * Save constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param MenuStores $menuStores
     * @param CollectionFactory $collectionFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param Registry $coreRegistry
     * @param TypeListInterface $cacheTypeList
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        MenuStores $menuStores,
        CollectionFactory $collectionFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        Registry $coreRegistry,
        TypeListInterface $cacheTypeList
    ) {
        parent::__construct($context);
        $this->backendSession = $context->getSession();
        $this->menuStores = $menuStores;
        $this->collectionFactory = $collectionFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->coreRegistry = $coreRegistry;
        $this->cacheTypeList = $cacheTypeList;
    }

    /**
     * Save data post
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        if (!$data) {
            $resultRedirect->setPath('*/*/new');
            return $resultRedirect;
        }

        try {
            $storeId = is_array($data['store_id']) ? trim(implode(",", $data['store_id'])) : "";

            $data['store_id'] = $storeId;
            $storeItemsCollection = $this->collectionFactory->create();

            $categoryStore = $this->menuStores;
            if ($data['category_store_id']) {
                $categoryStore->load($data['category_store_id']);
                $storeItemsCollection->addFieldToFilter('category_store_id', $data['category_store_id']);
                if ($storeItemsCollection->getData()) {
                    foreach ($storeItemsCollection as $value) {
                        $value->setStoreId($storeId);
                        $value->setPriority($data['priority']);
                    }
                    $storeItemsCollection->save();
                }
            } else {
                unset($data['category_store_id']);
            }
            $categoryStore->setData($data);
            if (isset($data['id'])) {
                $categoryStore->setId($data['id']);
            }
            $categoryStore->save();
            // mark cache need clean
            $this->cacheTypeList->invalidate(
                \Magento\PageCache\Model\Cache\Type::TYPE_IDENTIFIER
            );
            $this->messageManager->addSuccessMessage(__('Menu has been successfully saved.'));
            $this->backendSession->setBssBlogcommentData(false);

            if ($this->getRequest()->getParam('back')) {
                $resultRedirect->setPath(
                    '*/*/edit',
                    [
                        'id' => $categoryStore->getId(),
                        '_current' => true
                    ]
                );

                return $resultRedirect;
            }

            $resultRedirect->setPath('*/*/index');
            return $resultRedirect;
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
        }

        $categoryStore = $this->menuStores;
        $this->backendSession->setBssMegamenuStoreData($data);
        $resultRedirect->setPath(
            '*/*/edit',
            [
                'id' => $categoryStore->getId(),
                '_current' => true
            ]
        );

        return $resultRedirect;
    }
}
