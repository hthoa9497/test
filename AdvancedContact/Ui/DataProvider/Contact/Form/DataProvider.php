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

namespace Magezon\AdvancedContact\Ui\DataProvider\Contact\Form;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magezon\AdvancedContact\Model\ResourceModel\Contact\CollectionFactory;

class DataProvider extends \Magento\Ui\DataProvider\ModifierPoolDataProvider
{
    /**
     * @var CollectionFactory
     */
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * DataProvider constructor.
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param DataPersistorInterface $dataPersistor
     * @param CollectionFactory $collectionFactory
     * @param array $meta
     * @param array $data
     * @param PoolInterface|null $pool
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        DataPersistorInterface $dataPersistor,
        CollectionFactory $collectionFactory,
        PoolInterface $pool = null,
        array $meta = [],
        array $data = []
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->collection = $collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data, $pool);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();

        $data = $this->dataPersistor->get('dataContact');
        foreach ($items as $file) {
            $this->loadedData[$file->getId()] = $file->getData();
        }

        if (!empty($data)) {
            $file = $this->collection->getNewEmptyItem();
            $file->setData($data);
            $this->loadedData[$file->getId()] = $file->getData();
            $this->dataPersistor->clear('dataContact');
        }

        return $this->loadedData;
    }
}
