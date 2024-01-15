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

namespace Magezon\AdvancedContact\Ui\DataProvider\Contact;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Api\Filter;
use Magezon\AdvancedContact\Model\ResourceModel\Contact\CollectionFactory;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var CollectionFactory
     */
    protected $collection;

    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $colectionFactory
     * @param array $meta
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $colectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $meta,
            $data
        );

        $this->collection = $colectionFactory->create();
    }

    /**
     * @inheritdoc
     */
    public function addFilter(Filter $filter)
    {
        if ($filter->getField() == 'fulltext') {
            $filter->setConditionType('like');
            $filter->setField('main_table.name');
            $filter->setValue('%' . $filter->getValue() . '%');
            parent::addFilter($filter);
        } else {
            parent::addFilter($filter);
        }
    }
}
