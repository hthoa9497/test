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

namespace Magezon\AdvancedContact\Helper;

use Magento\Framework\UrlInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Contact\Model\ConfigInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_PUBLIC_KEY = 'recaptcha_frontend/type_invisible/public_key';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        Context $context,
        UrlInterface $urlBuilder,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->urlBuilder = $urlBuilder;
        $this->storeManager = $storeManager;
    }

    /**
     * @param string $key
     * @param null|int $_store
     * @return null|string
     */
    public function getConfig($key, $store = null)
    {
        $store = $this->storeManager->getStore($store);
        $result = $this->scopeConfig->getValue(
            'advancedcontact/' . $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        return $result;
    }

    /**
     * Get status auto response
     * @return bool
     */
    public function isEnabledResponse()
    {
        return $this->getConfig('autoresponder/enabled');
    }

    /**
     * Get status module
     * @return bool
     */
    public function isEnabled()
    {
        return $this->getConfig('general/enabled');
    }

    /**
     * Get icon loading
     * @return string
     */
    public function getIconLoading()
    {
        $nameImage = $this->getConfig('general/icon_load');
        if (!empty($nameImage)) {
            return $this->getMediaUrl() . 'advancedcontact/image/' . $nameImage;
        }
    }

    /**
     * Get file url
     * @return string
     */
    public function getMediaUrl()
    {
        return $this->urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]);
    }

    /**
     * Get email template
     * @return string
     */
    public function getEmailTemplate()
    {
        return $this->getConfig('autoresponder/email_template');
    }

    /**
     * Get reply email template
     * @return string|null
     */
    public function getReplyEmailTemplate()
    {
        return $this->getConfig('replying_mail/email_template');
    }

    /**
     * @return string
     */
    public function getContactSenderName()
    {
        $sender = $this->scopeConfig->getValue(ConfigInterface::XML_PATH_EMAIL_SENDER, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $this->scopeConfig->getValue('trans_email/ident_' . $sender . '/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getSecurityCaptchaKey()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PUBLIC_KEY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
