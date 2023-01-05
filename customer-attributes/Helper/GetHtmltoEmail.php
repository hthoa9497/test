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

namespace Bss\CustomerAttributes\Helper;

use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Attribute;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerFactory;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @SuppressWarnings(PHPMD)
 */
class GetHtmltoEmail extends \Magento\Framework\App\Helper\AbstractHelper
{
    public const CUSTOMER_ADDRESS = 'customer_address';
    public const CUSTOMER = 'customer';

    /**
     * Store factory
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CustomerMetadataInterface
     */
    protected $metadata;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var SaveObject
     */
    protected $saveObject;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;
    /**
     * @var Json
     */
    private $json;
    /**
     * @var AttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * @var Customerattribute
     */
    protected $helper;

    /**
     * Get Html to Email constructor.
     *
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param CustomerMetadataInterface $metadata
     * @param CustomerRepositoryInterface $customerRepository
     * @param SaveObject $saveObject
     * @param CustomerFactory $customerFactory
     * @param Json $json
     * @param AttributeRepositoryInterface $attributeRepository
     * @param Customerattribute $helper
     */
    public function __construct(
        Context                      $context,
        StoreManagerInterface        $storeManager,
        CustomerMetadataInterface    $metadata,
        CustomerRepositoryInterface  $customerRepository,
        SaveObject                   $saveObject,
        CustomerFactory              $customerFactory,
        Json                         $json,
        AttributeRepositoryInterface $attributeRepository,
        Customerattribute            $helper
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->metadata = $metadata;
        $this->customerRepository = $customerRepository;
        $this->urlEncoder = $context->getUrlEncoder();
        $this->saveObject = $saveObject;
        $this->customerFactory = $customerFactory;
        $this->json = $json;
        $this->attributeRepository = $attributeRepository;
        $this->helper = $helper;
    }

    /**
     * Get Config
     *
     * @param string $path
     * @param int $store
     * @param string $scope
     * @return mixed
     */
    public function getConfig($path, $store = null, $scope = null)
    {
        if ($scope === null) {
            $scope = ScopeInterface::SCOPE_STORE;
        }
        return $this->scopeConfig->getValue($path, $scope, $store);
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return (string)$this->getConfig('bss_customer_attribute/general/title');
    }

    /**
     * @param string $attributeCode
     * @return bool
     * @throws LocalizedException
     */
    public function isAttribureAddtoEmail($attributeCode)
    {
        $attribute = $this->attributeRepository->get(self::CUSTOMER, $attributeCode);
        $usedInForms = $attribute->getUsedInForms();

        if (in_array('show_in_email', $usedInForms)) {
            return true;
        }
        return false;
    }

    /**
     * @param $attribute
     * @return bool
     */
    public function isAddressAddToOrderEmail($attribute)
    {
        $usedInForms = $attribute->getUsedInForms();
        if (in_array('show_in_order_email', $usedInForms)) {
            return true;
        }
        return false;
    }

    /**
     * @param $attribute
     * @return bool
     */
    public function isAddressAddToInvoiceEmail($attribute)
    {
        $usedInForms = $attribute->getUsedInForms();
        if (in_array('show_in_invoice_email', $usedInForms)) {
            return true;
        }
        return false;
    }

    /**
     * @param $attribute
     * @return bool
     */
    public function isAddressAddToShipmentEmail($attribute)
    {
        $usedInForms = $attribute->getUsedInForms();
        if (in_array('show_in_shipping_email', $usedInForms)) {
            return true;
        }
        return false;
    }

    /**
     * @param $attribute
     * @return bool
     */
    public function isAddressAddToCreditMemoEmail($attribute)
    {
        $usedInForms = $attribute->getUsedInForms();
        if (in_array('show_in_credit_memo_email', $usedInForms)) {
            return true;
        }
        return false;
    }

    /**
     * @param string $attributeCode
     * @return bool
     * @throws LocalizedException
     */
    public function isAttribureAddtoEmailNewAccount($attributeCode)
    {
        $attribute = $this->attributeRepository->get(self::CUSTOMER, $attributeCode);
        $usedInForms = $attribute->getUsedInForms();

        if (in_array('show_in_email_new_account', $usedInForms)) {
            return true;
        }
        return false;
    }

    /**
     * @return int
     * @throws NoSuchEntityException
     */
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getStoreId();
    }

    /**
     * @param Customer $customer
     * @param Attribute $attributes
     * @return bool
     * @throws LocalizedException
     */
    public function hasDataCustomerAttributesEmail($customer, $attributes)
    {
        foreach ($attributes as $attribute) {
            if ($attribute->isSystem() || !$attribute->isUserDefined()) {
                continue;
            }
            if ($this->isAttribureAddtoEmail($attribute->getAttributeCode())) {
                if ($customer->getCustomAttribute($attribute->getAttributeCode())) {
                    if ($customer->getCustomAttribute($attribute->getAttributeCode())->getValue() != '') {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * @param Customer $customer
     * @param Attribute $attributes
     * @return bool
     * @throws LocalizedException
     */
    public function hasDataCustomerAttributesEmailNewAccount($customer, $attributes)
    {
        foreach ($attributes as $attribute) {
            if ($attribute->isSystem() || !$attribute->isUserDefined()) {
                continue;
            }
            if ($this->isAttribureAddtoEmailNewAccount($attribute->getAttributeCode())) {
                if ($customer->getCustomAttribute($attribute->getAttributeCode())) {
                    if ($customer->getCustomAttribute($attribute->getAttributeCode())->getValue() != '') {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * @param int $idCustomer
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getVariableEmailHtml($idCustomer)
    {
        $html = '';
        if ($this->getConfig('bss_customer_attribute/general/enable') && $idCustomer) {
            $entityTypeId = 'customer';
            $attributes = $this->metadata->getAllAttributesMetadata($entityTypeId);
            $customer = $this->customerFactory->create()->load($idCustomer);
            $customerData = $customer->getDataModel();
            if ($this->hasDataCustomerAttributesEmail($customerData, $attributes)) {
                $html = '<h3>' . $this->getTitle() . '</h3>';
                foreach ($attributes as $attribute) {
                    if ($attribute->isSystem() || !$attribute->isUserDefined() || !$attribute->isVisible()) {
                        continue;
                    }
                    if ($this->isAttribureAddtoEmail($attribute->getAttributeCode())) {
                        if ($customerData->getCustomAttribute($attribute->getAttributeCode())) {
                            $format = str_replace(
                                " 00:00:00",
                                "",
                                $customerData->getCustomAttribute($attribute->getAttributeCode())->getValue()
                            );
                            if ($attribute->getFrontendInput() == 'date') {
                                $customerData->getCustomAttribute($attribute->getAttributeCode())
                                    ->setValue($this->helper->formatDate($format));
                            }
                            $html .= $this->getValueAttributetoEmail($attribute, $customerData);
                        }
                    }
                }
            }
        }
        return $html;
    }

    /**
     * @param string $customAddress
     * @param $idCustomer
     * @return string
     * @throws LocalizedException
     */
    public function getAddressVariableOrderEmailHtml($customAddress, $idCustomer)
    {
        $html = '';
        if ($this->getConfig('bss_customer_attribute/general/enable') && $idCustomer) {
            if ($customAddress) {
                $customAddress = $this->json->unserialize($customAddress);
                $html = '<h4>' . $this->getTitle() . '</h4>';
                foreach ($customAddress as $attributeCode => $attributeValue) {
                    $attribute = $this->attributeRepository->get(self::CUSTOMER_ADDRESS, $attributeCode);
                    if ($this->isAddressAddToOrderEmail($attribute)) {
                        if ($attribute->getFrontendInput() == 'date') {
                            $format = str_replace(
                                " 00:00:00",
                                "",
                                $attributeValue['value']
                            );
                            $html .= $this->getValueCustomAddressToEmail(
                                $attribute,
                                $this->helper->formatDate($format)
                            );
                        } else {
                            $html .= $this->getValueCustomAddressToEmail($attribute, $attributeValue['value']);
                        }
                    }
                }
            }
        }
        return $html;
    }

    /**
     * @param string $customAddress
     * @param $idCustomer
     * @return string
     * @throws LocalizedException
     */
    public function getAddressVariableGuestEmailHtml($customAddress)
    {
        $html = '';
        if ($this->getConfig('bss_customer_attribute/general/enable')) {
            if ($customAddress) {
                $customAddress = $this->json->unserialize($customAddress);
                $html = '<h4>' . $this->getTitle() . '</h4>';
                foreach ($customAddress as $attributeCode => $attributeValue) {
                    $attribute = $this->attributeRepository->get(self::CUSTOMER_ADDRESS, $attributeCode);
                    if ($this->isAddressAddToOrderEmail($attribute)) {
                        if ($attribute->getFrontendInput() == 'date') {
                            $format = str_replace(
                                " 00:00:00",
                                "",
                                $attributeValue['value']
                            );
                            $html .= $this->getValueCustomAddressToEmail(
                                $attribute,
                                $this->helper->formatDate($format)
                            );
                        } else {
                            $html .= $this->getValueCustomAddressToEmail($attribute, $attributeValue['value']);
                        }
                    }
                }
            }
        }
        return $html;
    }

    /**
     * @param string $customAddress
     * @param $idCustomer
     * @return string
     * @throws LocalizedException
     */
    public function getAddressVariableShipmentEmailHtml($customAddress, $idCustomer)
    {
        $html = '';
        if ($this->getConfig('bss_customer_attribute/general/enable') && $idCustomer) {
            if ($customAddress) {
                $customAddress = $this->json->unserialize($customAddress);
                $html = '<h4>' . $this->getTitle() . '</h4>';
                foreach ($customAddress as $attributeCode => $attributeValue) {
                    $attribute = $this->attributeRepository->get(self::CUSTOMER_ADDRESS, $attributeCode);
                    if ($this->isAddressAddToShipmentEmail($attribute)) {
                        if ($attribute->getFrontendInput() == 'date') {
                            $format = str_replace(
                                " 00:00:00",
                                "",
                                $attributeValue['value']
                            );
                            $html .= $this->getValueCustomAddressToEmail(
                                $attribute,
                                $this->helper->formatDate($format)
                            );
                        } else {
                            $html .= $this->getValueCustomAddressToEmail($attribute, $attributeValue['value']);
                        }
                    }
                }
            }
        }
        return $html;
    }

    /**
     * @param string $customAddress
     * @param $idCustomer
     * @return string
     * @throws LocalizedException
     */
    public function getAddressVariableInvoiceEmailHtml($customAddress, $idCustomer)
    {
        $html = '';
        if ($this->getConfig('bss_customer_attribute/general/enable') && $idCustomer) {
            if ($customAddress) {
                $customAddress = $this->json->unserialize($customAddress);
                $html = '<h4>' . $this->getTitle() . '</h4>';
                foreach ($customAddress as $attributeCode => $attributeValue) {
                    $attribute = $this->attributeRepository->get(self::CUSTOMER_ADDRESS, $attributeCode);
                    if ($this->isAddressAddToInvoiceEmail($attribute)) {
                        if ($attribute->getFrontendInput() == 'date') {
                            $format = str_replace(
                                " 00:00:00",
                                "",
                                $attributeValue['value']
                            );
                            $html .= $this->getValueCustomAddressToEmail(
                                $attribute,
                                $this->helper->formatDate($format)
                            );
                        } else {
                            $html .= $this->getValueCustomAddressToEmail($attribute, $attributeValue['value']);
                        }
                    }
                }
            }
        }
        return $html;
    }

    /**
     * @param string $customAddress
     * @param $idCustomer
     * @return string
     * @throws LocalizedException
     */
    public function getAddressVariableCreditMemoEmailHtml($customAddress, $idCustomer)
    {
        $html = '';
        if ($this->getConfig('bss_customer_attribute/general/enable') && $idCustomer) {
            if ($customAddress) {
                $customAddress = $this->json->unserialize($customAddress);
                $html = '<h4>' . $this->getTitle() . '</h4>';
                foreach ($customAddress as $attributeCode => $attributeValue) {
                    $attribute = $this->attributeRepository->get(self::CUSTOMER_ADDRESS, $attributeCode);
                    if ($this->isAddressAddToCreditMemoEmail($attribute)) {
                        if ($attribute->getFrontendInput() == 'date') {
                            $format = str_replace(
                                " 00:00:00",
                                "",
                                $attributeValue['value']
                            );
                            $html .= $this->getValueCustomAddressToEmail(
                                $attribute,
                                $this->helper->formatDate($format)
                            );
                        } else {
                            $html .= $this->getValueCustomAddressToEmail($attribute, $attributeValue['value']);
                        }
                    }
                }
            }
        }
        return $html;
    }

    /**
     * @param int $idCustomer
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getVariableEmailNewAccountHtml($idCustomer)
    {
        $html = '';
        if ($this->getConfig('bss_customer_attribute/general/enable') && $idCustomer) {
            $entityTypeId = 'customer';
            $attributes = $this->metadata->getAllAttributesMetadata($entityTypeId);
            $customer = $this->customerRepository->getById($idCustomer);
            if ($this->hasDataCustomerAttributesEmailNewAccount($customer, $attributes)) {
                $html = '<h3>' . $this->getTitle() . '</h3>';
                foreach ($attributes as $attribute) {
                    if ($attribute->isSystem() || !$attribute->isUserDefined() || !$attribute->isVisible()) {
                        continue;
                    }
                    if ($this->isAttribureAddtoEmailNewAccount($attribute->getAttributeCode())) {
                        if ($customer->getCustomAttribute($attribute->getAttributeCode())) {
                            foreach ($customer->getCustomAttributes() as $attributeValue) {
                                if ($attribute->getFrontendInput() == 'date') {
                                    $attributeCode = $attribute->getAttributeCode();

                                    if ($attributeValue->getAttributeCode() == $attributeCode) {
                                        $format = str_replace(
                                            " 00:00:00",
                                            "",
                                            $attributeValue->getValue()
                                        );
                                        $attributeValue->setValue($this->helper->formatDate($format));
                                    }
                                }
                            }
                            $html .= $this->getValueAttributetoEmail($attribute, $customer);
                        }
                    }
                }
            }
        }
        return $html;
    }

    /**
     * @param Attribute $attribute
     * @param Customer $customer
     * @return string
     */
    private function getValueAttributetoEmail($attribute, $customer)
    {
        $html = '';
        if ($customer->getCustomAttribute($attribute->getAttributeCode())->getValue() != '') {
            if ($attribute->getOptions()) {
                $valueOption = $customer->getCustomAttribute($attribute->getAttributeCode())->getValue();
                if ($valueOption) {
                    $valueOption = explode(",", $valueOption);
                } else {
                    $valueOption = [];
                }
                $label = "";
                foreach ($valueOption as $value) {
                    foreach ($attribute->getOptions() as $option) {
                        if ($value == $option->getValue()) {
                            $label .= $option->getLabel() . ",";
                        }
                    }
                }
                $html .= "<div class=\"orderAttribute\"><div class=\"label_attribute\"><span>" .
                    $attribute->getFrontendLabel() . ': ' . "</span></div>" . "<div class=\"value_attribute\"><span>" .
                    rtrim($label, ",") .
                    "</span></div></div><br/>";
            } else {
                $valueAttribute = $customer->getCustomAttribute($attribute->getAttributeCode())->getValue();
                $html .= $this->getAttributeFileinEmail($attribute, $valueAttribute);
            }
        }
        return $html;
    }

    /**
     * @param $attribute
     * @param string $attributeValue
     * @return string
     */
    private function getValueCustomAddressToEmail($attribute, $attributeValue)
    {
        $html = '';
        if ($attributeValue !== '') {
            $html .= "<div class=\"orderAddressAttribute\"><span>" .
                $attribute->getFrontendLabel() . ': ' . "</span>" . "<span>" .
                $attributeValue . "</span></div>";
        }
        return $html;
    }

    /**
     * @param Attribute $attribute
     * @param string $valueAttribute
     * @return string
     */
    private function getAttributeFileinEmail($attribute, $valueAttribute)
    {
        $html = "";
        if ($attribute->getFrontendInput() == 'file') {
            if (!$this->getConfig("bss_customer_attribute/general/allow_download_file")) {
                $noDownloadFile = "class=\"disabled\"";
            } else {
                $noDownloadFile = " ";
            }

            if (preg_match("/\.(gif|png|jpg)$/", $valueAttribute)) {
                $html .= $this->getFileImageFrontend($attribute, $valueAttribute);
            } elseif (preg_match("/\.(mp4|3gb|mov|mpeg)$/", $valueAttribute)) {
                $html .= $this->getFileVideoAudiotoEmail($attribute, $valueAttribute);
            } elseif (preg_match("/\.(mp3|ogg|wav)$/", $valueAttribute)) {
                $html .= $this->getFileVideoAudiotoEmail($attribute, $valueAttribute);
            } else {
                $html .= $this->getFileOtherFrontend($attribute, $valueAttribute, $noDownloadFile);
            }
        } else {
            $html .= "<div class=\"orderAttribute\"><div class=\"label_attribute\"><span>" .
                $attribute->getFrontendLabel() . ': ' . "</span></div>" . "<div class=\"value_attribute\"><span>" .
                $valueAttribute . "</span></div></div><br/>";
        }

        return $html;
    }

    /**
     * @param Attribute $attribute
     * @param string $valueAttribute
     * @return string
     */
    private function getFileImageFrontend($attribute, $valueAttribute)
    {
        $tagA = "";
        $endTagA = "";
        if ($this->getConfig("bss_customer_attribute/general/allow_download_file")) {
            $tagA = "<a href=\"" . $this->getViewFile($valueAttribute) . "\"" . " target=\"_blank\" >";
            $endTagA = "</a>";
        }
        $html = "<div class=\"orderAttribute\"><div class=\"label_attribute\"><span>" .
            $attribute->getFrontendLabel() . ': ' . "</span></div>" .
            $tagA . "<div class=\"value_attribute\"><img src=\"" .
            $this->getViewFile($valueAttribute) . "\" alt=\""
            . $this->getFileName($valueAttribute) . "\" width=\"200\" /></div>" .
            "</div>" . $endTagA . "<br/>";
        return $html;
    }

    /**
     * @param Attribute $attribute
     * @param string $valueAttribute
     * @return string
     */
    private function getFileVideoAudiotoEmail($attribute, $valueAttribute)
    {
        $html = "<div class=\"orderAttribute\"><div class=\"label_attribute\"><span>" .
            $attribute->getFrontendLabel() . ': ' .
            "</span></div>" . "<div class=\"value_attribute\"><a href=\"" . $this->getViewFile($valueAttribute) .
            "\">" . $this->getFileName($valueAttribute) . "</a>
            </div>" .
            "</div><br/>";

        return $html;
    }

    /**
     * @param Attribute $attribute
     * @param string $valueAttribute
     * @param string $noDownloadFile
     * @return string
     */
    private function getFileOtherFrontend($attribute, $valueAttribute, $noDownloadFile)
    {
        $html = "<div class=\"orderAttribute\"><div class=\"label_attribute\">
            <span>" . $attribute->getFrontendLabel() . ': ' . "</span>
            </div>" . "<div class=\"value_attribute\"><span>" .
            "<a href=\"" . $this->getViewFile($valueAttribute) . "\"" . " " . $noDownloadFile . " " .
            "target=\"_blank\">" . $this->getFileName($valueAttribute) . "</a>
            </span></div></div><br/>";

        return $html;
    }

    /**
     * Return escaped value
     *
     * @param string $fieldValue
     * @return string
     */
    public function getViewFile($fieldValue)
    {
        if ($fieldValue) {
            return $this->_getUrl(
                'customerattribute/index/viewfile',
                [
                    'file' => $this->urlEncoder->encode($fieldValue)
                ]
            );
        }
        return $fieldValue;
    }

    /**
     * @param string $filename
     * @return string
     */
    public function getFileName($filename)
    {
        if ($filename && strpos($filename, "/") !== false) {
            $nameArr = explode("/", $filename);
            return end($nameArr);
        }
        return $filename;
    }
}
