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
 * @package    Bss_CustomerAttributes
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\CustomerAttributes\Block\Form\AddressField;

use Bss\CustomerAttributes\Model\HandleData;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\Helper\SecureHtmlRenderer;
use Bss\CustomerAttributes\Block\Adminhtml\Address\Edit\Tab\Attribute\AddressAttribute;
use Bss\CustomerAttributes\Controller\Adminhtml\AddressAttribute\Edit;
use Bss\CustomerAttributes\Helper\Customer\Grid\NotDisplay;
use Bss\CustomerAttributes\Model\AddressAttributeDependent;
use Bss\CustomerAttributes\Model\SerializeData;
use Bss\CustomerAttributes\Model\ResourceModel\Option\Collection as BssCollection;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DynamicRow extends AbstractFieldArray
{
    /**
     * @var DynamicRow
     */
    private $optionsRenderer;

    /**
     * @var DynamicRow
     */
    private $dependentRenderer;

    /**
     * @var Edit
     */
    protected $collection;

    /**
     * @var NotDisplay
     */
    private $getAttributes;
    /**
     * @var BssCollection
     */
    private $optionAddressCollection;

    /**
     * @var SerializeData
     */
    private $serializer;

    /**
     * @var HandleData
     */
    private $handleData;

    /**
     * @param Edit $collection
     * @param NotDisplay $getAttributes
     * @param Context $context
     * @param BssCollection $optionAddressCollection
     * @param SerializeData $serializer
     * @param HandleData $handleData
     * @param array $data
     * @param SecureHtmlRenderer|null $secureRenderer
     */
    public function __construct(
        Edit                $collection,
        NotDisplay          $getAttributes,
        Context             $context,
        BssCollection       $optionAddressCollection,
        SerializeData       $serializer,
        HandleData          $handleData,
        array               $data = [],
        ?SecureHtmlRenderer $secureRenderer = null
    ) {
        $this->optionAddressCollection = $optionAddressCollection;
        parent::__construct($context, $data, $secureRenderer);
        $this->collection = $collection;
        $this->getAttributes = $getAttributes;
        $this->serializer = $serializer;
        $this->handleData = $handleData;
    }

    /**
     * Render add Column
     *
     * @throws LocalizedException
     */
    protected function _prepareToRender()
    {
        $this->addColumn('attribute_value', ['label' => __('Attribute Value'),
            'class' => 'required-entry ',
            'renderer' => $this->getAttributeValuesRenderer()
        ]);
        $this->addColumn('dependent_attribute', ['label' => __('Dependent Attribute'),
            'class' => 'required-entry ',
            'renderer' => $this->getDependentAttributeRenderer()
        ]);
        $this->setTemplate('Bss_CustomerAttributes::customer/attribute/array.phtml');
        $this->_addAfter = false;
    }

    /**
     * Get Attribute Values to Render
     *
     * @return AddressAttribute|BlockInterface
     * @throws LocalizedException
     */
    private function getAttributeValuesRenderer()
    {
        if (!$this->optionsRenderer) {
            $this->optionsRenderer = $this->getLayout()->createBlock(
                AddressAttribute::class,
                'address_attribute'
            );
        }
        return $this->optionsRenderer;
    }

    /**
     * Get Dependent Attribute
     *
     * @return BlockInterface
     * @throws LocalizedException
     */
    private function getDependentAttributeRenderer()
    {
        if (!$this->dependentRenderer) {
            $this->dependentRenderer = $this->getLayout()->createBlock(
                \Bss\CustomerAttributes\Block\Adminhtml\Address\Edit\Tab\Relation\DependentAddressAttribute::class,
                'dependent_address_attribute'
            );
        }
        return $this->dependentRenderer;
    }

    /**
     * Get Post Data
     *
     * @return AddressAttributeDependent
     */
    public function getDataAttribute()
    {
        return $this->collection->getCollection();
    }

    /**
     * Get All Attributes Collection
     *
     * @return array|AbstractDb|AbstractCollection
     */
    public function getAllAttributesCollection()
    {
        return $this->getAttributes->getAllAddressCollection();
    }

    /**
     * Get Attribute By Code
     *
     * @param mixed $attributeCode
     * @return array|AbstractDb|AbstractCollection
     */
    public function getAttributeByCode($attributeCode)
    {
        return $this->getAttributes->getAddressAttributeByCode($attributeCode);
    }

    /**
     * Get Attribute By Id
     *
     * @return array|AbstractDb|AbstractCollection|null
     */
    public function getAttributeById()
    {
        return $this->collection->getAttributeId();
    }

    /**
     * Get Option Value By Id
     *
     * @param mixed $optionValue
     * @return Collection
     */
    public function getOptionValueById($optionValue)
    {
        return $this->optionAddressCollection->getOptionValueById($optionValue);
    }

    /**
     * Get All Attribute Dependent Information in Be
     *
     * @param array|mixed $attributes
     * @return array
     */
    public function getAllAttributeDependentBe($attributes)
    {
        return $this->handleData->getAllAttributeDependentBe($attributes);
    }

    /**
     * Validate All Attribute Dependent BE
     *
     * @param array|mixed $blockObj
     * @param int $customerAttributeId
     * @return mixed
     */
    public function validateAllAttributeDependentBe($blockObj, $customerAttributeId)
    {
        return $this->handleData->validateAllAttributeDependentBe($blockObj, $customerAttributeId);
    }

    /**
     * Encode function
     *
     * @param mixed|array $data
     * @return bool|string
     */
    public function encodeFunction($data)
    {
        return $this->serializer->encodeFunction($data);
    }

    /**
     * Decode function
     *
     * @param mixed|array $data
     * @return array|bool|float|int|string|null
     */
    public function decodeFunction($data)
    {
        return $this->serializer->decodeFunction($data);
    }
}
