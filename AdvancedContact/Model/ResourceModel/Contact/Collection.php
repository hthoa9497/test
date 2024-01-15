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
namespace Magezon\AdvancedContact\Model\ResourceModel\Contact;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'contact_id';

    /**
     * @var string
     */
    protected $_eventPrefix = 'mgz_advanced_contact_collection';
    
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Magezon\AdvancedContact\Model\Contact::class,
            \Magezon\AdvancedContact\Model\ResourceModel\Contact::class
        );
    }
}
