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
    protected $advancedContactFactory;

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory 
     */
    protected $uploaderFactory;

    /**
     * @var \Magento\Framework\Filesystem 
     */
    protected $fileSystem;

    /**
     * @var \Magento\Framework\Message\ManagerInterface 
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory 
     */
    protected $resultRedirect;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magezon\AdvancedContact\Helper\Data $helperData
     * @param \Magezon\AdvancedContact\Model\Email $email
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\Controller\Result\RedirectFactory $resultRedirect
     * @param \Magezon\AdvancedContact\Model\ContactFactory $advancedContactFactory
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager, 
        \Magezon\AdvancedContact\Helper\Data $helperData,
        \Magezon\AdvancedContact\Model\Email $email,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirect,
        \Magezon\AdvancedContact\Model\ContactFactory $advancedContactFactory
    ) {
        $this->storeManager = $storeManager; 
        $this->helperData = $helperData;
        $this->email = $email;
        $this->uploaderFactory = $uploaderFactory;
        $this->fileSystem = $filesystem;
        $this->messageManager = $messageManager;
        $this->resultRedirect = $resultRedirect;
        $this->advancedContactFactory = $advancedContactFactory;
    }

    /**
     * @param \Magento\Contact\Controller\Index\Post $subject
     * @throws \Exception
     */
    public function aroundExecute(\Magento\Contact\Controller\Index\Post $subject, \Closure $proceed)
    {   
        // check maxlength field input
        $data = $subject->getRequest()->getPostValue();
        if (strlen($data['name']) > 80) {
            $this->messageManager->addErrorMessage(
                __('Please enter less or equal than %1 symbols in the Name.', 80)
            );
            return $this->resultRedirect->create()->setPath('contact/index');
        }
        if (strlen($data['email']) > 255) {
            $this->messageManager->addErrorMessage(
                __('Please enter less or equal than %1 symbols in the Email.', 255)
            );
            return $this->resultRedirect->create()->setPath('contact/index');
        }
        if (strlen($data['comment']) > 2000) {
            $this->messageManager->addErrorMessage(
                __('Please enter less or equal than %1 symbols in the Comment.', 2000)
            );
            return $this->resultRedirect->create()->setPath('contact/index');
        }
        // check file size and file type
        $maxFileSize = \Magezon\AdvancedContact\Model\Contact::MAX_FILE_UPLOAD_SIZE;
        if ($_FILES['file_attach']['name']) {
            // check type
            if (!in_array(mime_content_type($_FILES['file_attach']['tmp_name']), \Magezon\AdvancedContact\Model\Contact::ALLOWED_FILE_TYPES)) {
                $this->messageManager->addErrorMessage(
                    __('File type is not allowed')
                );
                return $this->resultRedirect->create()->setPath('contact/index');
            }
            
            if ($_FILES['file_attach']['size'] > $maxFileSize) {
                $this->messageManager->addErrorMessage(
                    __('Files bigger than %1 MB not allowed', ceil($maxFileSize / 1048576))
                );
                return $this->resultRedirect->create()->setPath('contact/index');
            }
        }
        $result = $proceed();
        if ($this->helperData->isEnabled()) {
//            $data = $subject->getRequest()->getPostValue();
            $data['store_id'] = $this->getStoreId();
            if ($_FILES['file_attach']['name']) {
                $uploader = $this->uploaderFactory->create(['fileId' => 'file_attach']);
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(true);
                $mediaDirectory = $this->fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
                $path = $mediaDirectory->getAbsolutePath(\Magezon\AdvancedContact\Model\Contact::ATTACHMENT_UPLOAD_DIR);
                $imagePath = $uploader->save($path);
                $data['attachment'] = $imagePath['file'];
            }
            
            $this->advancedContactFactory->create()
                ->addData($data)
                ->save();

            // response customer
            $responseStatus = $this->helperData->isEnabledResponse();
            $templateId = $this->helperData->getEmailTemplate();
            if ($responseStatus) {
                $this->email->sendEmailByTemplate($templateId, $data['email'], $this->getStoreId());
            }
        }
        
        return $result;
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
