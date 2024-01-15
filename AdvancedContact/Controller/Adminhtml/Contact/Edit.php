<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_AdvancedContact
 * @copyright Copyright (C) 2020 Magezon (https://www.magezon.com)
 */
 
namespace Magezon\AdvancedContact\Controller\Adminhtml\Contact;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;
use Magezon\AdvancedContact\Model\ContactFactory;

class Edit extends Action implements HttpGetActionInterface
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var ContactFactory
     */
    protected $advancedContactFactory;

    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Registry $registry
     * @param ContactFactory $postFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $registry,
        ContactFactory $advancedContactFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry      = $registry;
        $this->advancedContactFactory = $advancedContactFactory;
    }

    /**
     * View action
     *
     * @return ResultInterface
     */

    public function execute()
    {
        $contactId = $this->getRequest()->getParam('contact_id');

        $model = $this->advancedContactFactory->create();
        $dataContact = $model->load($contactId);
        $this->coreRegistry->register('dataContact', $dataContact);

        if (!$dataContact->getId()) {
            $this->messageManager->addErrorMessage(__('Contact not exists.'));
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/');
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magezon_Core::extensions');
        $resultPage->getConfig()->getTitle()->prepend(__('Magezon contact'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? $model->getName() : __('Info Contact'));
        return $resultPage;
    }
}