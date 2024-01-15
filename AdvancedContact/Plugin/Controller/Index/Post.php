<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com/ for more information.
 *
 * @category  Magezon
 * @package   Magezon_AdvancedContact
 * @copyright Copyright (C) 2020 Magezon (https://www.magezon.com/)
 */

namespace Magezon\AdvancedContact\Plugin\Controller\Index;

class Post
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magezon\AdvancedContact\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Magezon\AdvancedContact\Model\Email
     */
    protected $email;

    /**
     * @var \Magezon\AdvancedContact\Model\ContactFactory
     */
    protected $contactFactory;

    /**
     * @param \Magezon\AdvancedContact\Helper\Data $helperData
     * @param \Magezon\AdvancedContact\Model\Email $email
     * @param \Magezon\AdvancedContact\Model\ContactFactory $contactFactory
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager, 
        \Magezon\AdvancedContact\Helper\Data $helperData,
        \Magezon\AdvancedContact\Model\Email $email,
        \Magezon\AdvancedContact\Model\ContactFactory $contactFactory
    ) {
        $this->storeManager = $storeManager; 
        $this->helperData = $helperData;
        $this->email = $email;
        $this->contactFactory = $contactFactory;
    }

    /**
     * @param \Magento\Contact\Controller\Index\Post $subject
     * @throws \Exception
     */
    public function afterExecute(\Magento\Contact\Controller\Index\Post $subject, $result)
    {   
        if ($this->helperData->isEnabled()) {
            $data = $subject->getRequest()->getPostValue();
            $data['store_id'] = $this->getStoreId();
            $this->contactFactory->create()
                ->addData($data)
                ->save();

            // response customer
            $responseStatus = $this->helperData->isEnabledResponse();
            $templateId = $this->helperData->getEmailTemplate();
            if ($responseStatus) {
                $this->email->sendEmailByTemplate($templateId, $data['email']);
            }
        } else {
            return $result;
        }
    }

    /**
     * Get website identifier
     *
     * @return string|int|null
     */
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }
}
