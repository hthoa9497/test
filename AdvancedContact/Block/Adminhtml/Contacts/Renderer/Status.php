<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_AdvancedContact
 * @copyright Copyright (C) 2020 Magezon (https://www.magezon.com)
 */

namespace Magezon\AdvancedContact\Block\Adminhtml\Contacts\Renderer;

use Magento\Ui\Component\Listing\Columns\Column;

class Status extends Column
{
    /** Status contact */
    const STATUS_ANSWERED = 2;
    const STATUS_CLOSED = 3;

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $availableOptions = $this->getOptions()->toOptionArray();
            foreach ($dataSource['data']['items'] as &$item) {
                foreach ($availableOptions as $k => $status) {
                    if (!isset($item['is_active'])) {
                        $item['is_active'] = 0;
                    }
                    if ($status['value'] == $item['is_active'] && is_numeric($item['is_active'])) {
                        $classFix = 'mgz-status_' . $status['value'];
                        if ($status['value'] == self::STATUS_ANSWERED) {
                            $classFix = 'mgz-status_1';
                        } else if ($status['value'] == self::STATUS_CLOSED) {
                            $classFix = 'mgz-status_3';
                        }
                        $item['is_active'] = '<span class="mgz-status ' . $classFix . '">' . $status['label'] . '</span>';
                        continue;
                    }
                }
            }
        }
        return $dataSource;
    }
}
