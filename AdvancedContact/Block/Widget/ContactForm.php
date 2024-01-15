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
namespace Magezon\AdvancedContact\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class ContactForm extends Template implements BlockInterface
{
    /**
     * Produce and return block's html output
     *
     * This method should not be overridden. You can override _toHtml() method in descendants if needed.
     *
     * @return string
     */
    public function toHtml()
    {
        $contactHtml = $this->getLayout()->createBlock('Magento\Contact\Block\ContactForm')
        ->setTemplate('Magezon_AdvancedContact::contactform.phtml')
        ->setCaptchaStatus($this->getData('gg_captcha'));
        $this->getLayout()->addContainer('form.additional.info', 'Form Additional Info');
        return $contactHtml->toHtml();
    }
}
