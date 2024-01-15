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

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;

class Contact extends AbstractModel implements IdentityInterface
{

    const CACHE_TAG = 'mgz_advanced_contact';
    const IS_ACTIVE = 'is_active';
    const STATUS_COLOSED = 3;
    const STATUS_ANSWERED = 1;
    const STATUS_PENDING  = 0;
    const ATTACHMENT_UPLOAD_DIR = 'contact/attachment';
    const ALLOWED_FILE_TYPES = ['image/jpeg', 'image/jpg', 'image/gif', 'image/png', 'application/pdf'];
    const MAX_FILE_UPLOAD_SIZE = (5*1024*1024) - 2335; // 5 MB - ~2.3Kb text field

    /**
     * @var string
     */
    protected $_cacheTag = 'mgz_advanced_contact';

    /**
     * @var string
     */
    protected $_eventPrefix = 'mgz_advanced_contact';

    /**
     * Initialize resource model
     *
     * @return void
     */

    protected function _construct()
    {
        $this->_init(\Magezon\AdvancedContact\Model\ResourceModel\Contact::class);
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Set contact status
     *
     * @param bool $status
     * @return $this
     */
    public function setIsActive($status)
    {
        return $this->setData(self::IS_ACTIVE, $status);
    }
}
