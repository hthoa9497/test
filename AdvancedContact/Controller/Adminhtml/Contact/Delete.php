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
use Magezon\AdvancedContact\Model\ContactFactory;

class Delete extends Action implements HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magezon_AdvancedContact::delete';

    /**
     * @var ContactFactory
     */
    protected $advancedContactFactory;

    /**
     * @param Context $context
     * @param ContactFactory $advancedContactFactory
     */
    public function __construct(
        Context $context,
        ContactFactory $advancedContactFactory
    ) {
        parent::__construct($context);
        $this->advancedContactFactory = $advancedContactFactory;
    }

    /**
     * Delete action
     *
     * @return Redirect
     */
    public function execute()
    {
        $contactId = $this->getRequest()->getParam('contact_id');

        try {
            $model = $this->advancedContactFactory->create();
            $model->load($contactId)->delete();
            $this->messageManager->addSuccess(__('Delete contact success.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('An error occurred !!!'));
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }
}
