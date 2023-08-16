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
namespace Bss\MegaMenu\Block\Adminhtml\Category\Edit\Tab;

use Bss\MegaMenu\Model\MenuStoresFactory;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store;

class General extends Generic implements TabInterface
{

    /**
     * @var string|null
     */
    public $store;
    /**
     * @var Store
     */
    protected $systemStore;

    /**
     * @var MenuStoresFactory
     */
    protected $menuStoresFactory;

    /**
     * General constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Store $systemStore
     * @param MenuStoresFactory $menuStoresFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Store $systemStore,
        MenuStoresFactory $menuStoresFactory,
        $data = []
    ) {
        $this->systemStore = $systemStore;
        $this->menuStoresFactory = $menuStoresFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form for tab
     *
     * @return Generic
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $cateId = $this->getRequest()->getParam('id');
        $menuFactory = $this->menuStoresFactory->create();
        $storeData = $menuFactory->load($cateId);
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('megamenu_store_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('General Information'),
                'class'  => 'fieldset-wide'
            ]
        );
        if ($storeData) {
            $rootName = $storeData->getName();
            $storeId = $storeData->getStoreId();
            $priority = $storeData->getPriority();
            $fieldset->addField(
                'category_store_id',
                'hidden',
                [
                    'name' => 'category_store_id',
                    'value' => $cateId
                ]
            );
        }

        $megaMenuStore = $this->_coreRegistry->registry('current_megamenu_store');

        $fieldset->addField(
            'name',
            'text',
            [
                'name'  => 'name',
                'label' => __('Root Menu Name'),
                'title' => __('Root Menu Name'),
                'value' => __($rootName ?? ''),
                'required' => true,
            ]
        );

        $fieldset->addField(
            'store_id',
            'multiselect',
            [
                'name'  => 'store_id',
                'label' => __('Choose store views'),
                'title' => __('Choose store views'),
                'value' => $storeId ?? '0',
                'values' => $this->systemStore->getStoreValuesForForm(false, true),
                'required' => true,
            ]
        );

        $fieldset->addField(
            'priority',
            'text',
            [
                'name'  => 'priority',
                'label' => __('Priority'),
                'title' => __('Priority'),
                'value' => $priority ?? 0,
                'required' => false,
            ]
        );

        $megaMenuStoreData = $this->_session->getData('bss_megamenu_store_data', true);
        if ($megaMenuStoreData) {
            $megaMenuStore->addData($megaMenuStoreData);
        }

        if ($megaMenuStore && $megaMenuStore->getData()) {
            $form->addValues($megaMenuStore->getData());
        }
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('General');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
