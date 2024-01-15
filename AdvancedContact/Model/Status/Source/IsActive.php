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
 * @package   Magezon_Core
 * @copyright Copyright (C) 2020 Magezon (https://www.magezon.com)
 */

namespace Magezon\AdvancedContact\Model\Status\Source;

use Magento\Framework\Data\OptionSourceInterface;

class IsActive implements OptionSourceInterface
{
    /**
     * Status contact
     */
    const STATUS_COLOSED = 3;
    const STATUS_ANSWERED = 1;
    const STATUS_PENDING  = 0;

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'label' => __('Answered'),
                'value' => self::STATUS_ANSWERED,
            ],
            [
                'label' => __('Pending'),
                'value' => self::STATUS_PENDING,
            ],
            [
                'label' => __('Closed'),
                'value' => self::STATUS_COLOSED,
            ]
        ];
        return $options;
    }
}
