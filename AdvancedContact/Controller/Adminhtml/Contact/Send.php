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

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magezon\AdvancedContact\Model\ContactFactory;
use Magezon\AdvancedContact\Model\EmailFactory;

class Send extends \Magento\Backend\App\Action implements \Magento\Framework\App\Action\HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magezon_AdvancedContact::send';

    /**
     * @var EmailFactory
     */
    protected $emailFactory;

    /**
     * @var ContactFactory
     */
    protected $contactFactory;

    /**
     * Send constructor.
     *
     * @param Context $context
     * @param ContactFactory $contactFactory
     * @param EmailFactory $emailFactory
     */
    public function __construct(
        Context $context,
        ContactFactory $contactFactory,
        EmailFactory $emailFactory
    ) {
        parent::__construct($context);
        $this->contactFactory = $contactFactory;
        $this->emailFactory = $emailFactory;
    }

    /**
     * send mail action
     *
     * @return Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $request = $this->getRequest()->getPostValue();

        if (empty($request['contact_id']) ||
            empty($request['content_email']) ||
            empty($request['title_email']) ||
            empty($request['email'])
        ) {
            $this->messageManager->addErrorMessage(__('Something is wrong with this field!'));
        } else {
            try {
                $storeName = $this->getStoreName();
                $email = $this->emailFactory->create();
                $email->sendEmail($request, $storeName);

                $model = $this->contactFactory->create();
                $model->load($request['contact_id']);
                $model->setData('is_active', $model::STATUS_ANSWERED);
                $model->save();

                $this->messageManager->addSuccess(__('Reply mail a success.'));
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__('Reply mail a error.'));
            }
        }

        return $resultRedirect->setPath('advancedcontactform/Contact');
    }

    /**
     * get store name
     *
     * @return mixed
     */
    public function getStoreName()
    {
        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get(\Magento\Store\Model\StoreManagerInterface::class);
        $storeName = $storeManager->getStore()->getName();
        return $storeName;
    }
}
