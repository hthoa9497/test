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

namespace Magezon\AdvancedContact\Block\Adminhtml\Tab\Edit\Button;

use Magento\Ui\Component\Control\Container;

class Send extends Generic
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        if (!$this->_isAllowedAction('Magezon_AdvancedContact::send')) {
            return [];
        }
        return [
            'label'          => __('Send'),
            'class'          => 'save primary',
            'class_name'     => Container::DEFAULT_CONTROL,
        ];
    }
}