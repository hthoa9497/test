<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_MegaMenu
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\MegaMenu\Model\Source;

class Stores extends \Magento\Store\Model\ResourceModel\Store\Collection
{
    /**
     * To Option Array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $data = $this->_toOptionArray('store_id', 'name');
        $data[] = ["value" => 0, "label" => __("All Store View")];
        return $data;
    }
}
