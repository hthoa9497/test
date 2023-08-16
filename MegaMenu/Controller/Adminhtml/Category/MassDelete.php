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

class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * Mass action filter
     *
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;

    /**
     * @var \Bss\MegaMenu\Model\ResourceModel\MenuStores\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Bss\MegaMenu\Model\MenuStoresFactory
     */
    protected $menuStoresFactory;

    /**
     * MassDelete constructor.
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Bss\MegaMenu\Model\ResourceModel\MenuStores\CollectionFactory $collectionFactory
     * @param \Bss\MegaMenu\Model\MenuStoresFactory $menuStoresFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Bss\MegaMenu\Model\ResourceModel\MenuStores\CollectionFactory $collectionFactory,
        \Bss\MegaMenu\Model\MenuStoresFactory $menuStoresFactory,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->menuStoresFactory = $menuStoresFactory;
        parent::__construct($context);
    }

    /**
     * Execute delete comment
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $i = 0;
            foreach ($collection as $item) {
                $menuModel = $this->menuStoresFactory->create();
                $this->loadMenuModel($menuModel, $item);
                $this->deleteMenuModel($menuModel);
                $i++;
            }

            $this->messageManager->addSuccessMessage(
                __('A total of %1 record(s) have been deleted.', $i)
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Load menu model
     *
     * @param object $menuModel
     * @param array $item
     * @return mixed
     */
    protected function loadMenuModel($menuModel, $item)
    {
        return $menuModel->load($item->getId());
    }

    /**
     * Delete menu
     *
     * @param object $menuModel
     * @return mixed
     */
    protected function deleteMenuModel($menuModel)
    {
        return $menuModel->delete();
    }
}
