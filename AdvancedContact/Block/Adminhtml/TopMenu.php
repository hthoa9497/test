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

namespace Magezon\AdvancedContact\Block\Adminhtml;

class TopMenu extends \Magezon\Core\Block\Adminhtml\TopMenu
{
    /**
     * Init menu items
     *
     * @return array
     */
    public function intLinks()
    {
        $links = [
            [
                [
                    'title' => __('Contacts'),
                    'link' => $this->getUrl('advancedcontactform/contact'),
                ]
            ],
            [
                [
                    'title' => __('Settings'),
                    'link' => $this->getUrl('adminhtml/system_config/edit/section/advancedcontact/'),
                ]
            ],
            [
                'class' => 'separator'
            ],
            [
                'title' => __('User Guide'),
                'link' => 'https://www.magezon.com/pub/media/productfile/advancedcontactform-user_guides.pdf',
                'target' => '_blank'
            ],
            [
                'title' => __('Change Log'),
                'link' => '',
                'target' => '_blank'
            ],
            [
                'title' => __('Get Support'),
                'link' => $this->getSupportLink(),
                'target' => '_blank'
            ]
        ];
        return $links;
    }
}
