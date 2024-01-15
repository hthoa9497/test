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
namespace Magezon\AdvancedContact\Model;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Contact\Model\ConfigInterface;
use Magezon\AdvancedContact\Helper\Data;

class Email extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var ConfigInterface
     */
    protected $contactsConfig;

    /**
     * @var Data 
     */
    protected $helperData;

    /**
     * @param \Magento\Backend\Block\Template\Context  $context
     * @param \Magento\Framework\Translate\Inline\StateInterface  $inlineTranslation
     * @param \Magento\Contact\Model\ConfigInterface  $contactsConfig
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param Data $helperData
     */
    public function __construct(
        Context $context,
        ConfigInterface $contactsConfig,
        StateInterface $inlineTranslation,
        Escaper $escaper,
        TransportBuilder $transportBuilder,
        Data $helperData
    ) {
        parent::__construct($context);
        $this->logger = $context->getLogger();
        $this->contactsConfig = $contactsConfig;
        $this->inlineTranslation = $inlineTranslation;
        $this->escaper = $escaper;
        $this->transportBuilder = $transportBuilder;
        $this->helperData = $helperData;
    }

    /**
     * Send to Email action
     * @param $dataRq
     * @param $nameSender
     */
    public function sendEmail($dataRq, $nameSender, $storeId)
    {
        try {
            $emailTemplate = $this->helperData->getReplyEmailTemplate();
            $emailRecipient = $this->contactsConfig->emailRecipient();
            $this->inlineTranslation->suspend();
            $sender = [
                'name' => $this->escaper->escapeHtml($nameSender),
                'email' => $this->escaper->escapeHtml($emailRecipient),
            ];
            $transport = $this->transportBuilder
                ->setTemplateIdentifier($emailTemplate)
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => $storeId,
                    ]
                )
                ->setTemplateVars([
                    'templateVar'  => $dataRq['content_email'],
                    'titlEmail' => $dataRq['title_email'],
                ])
                ->setFrom($sender)
                ->addTo($dataRq['email'])
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
    }

    /**
     * Send Email Template action
     * @param $id_temp
     * @param $toEmail
     */
    public function sendEmailByTemplate ($id_temp, $toEmail, $storeId = \Magento\Store\Model\Store::DEFAULT_STORE_ID)
    {
        try {
            $emailRecipient = $this->contactsConfig->emailRecipient();
            $from = ['email' => $this->escaper->escapeHtml($this->escaper->escapeHtml($emailRecipient)), 'name' => $this->getStoreName()];
            $this->inlineTranslation->suspend();

            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $templateOptions = [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $storeId,
            ];
            $transport = $this->transportBuilder->setTemplateIdentifier($id_temp, $storeScope)
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars([])
                ->setFrom($from)
                ->addTo($toEmail)
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->_logger->info($e->getMessage());
        }
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
