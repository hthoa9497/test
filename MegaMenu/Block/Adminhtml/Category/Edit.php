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
namespace Bss\MegaMenu\Block\Adminhtml\Category;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Edit constructor.
     *
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Block\Widget\Context $context,
        $data = []
    ) {
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize product Content  edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'category_store_id';
        $this->_blockGroup = 'Bss_MegaMenu';
        $this->_controller = 'adminhtml_category';
        parent::_construct();
        $this->buttonList->update('delete', 'label', __('Delete'));
        $this->buttonList->remove('reset');
        $this->buttonList->add(
            'save-and-continue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event' => 'saveAndContinueEdit',
                            'target' => '#edit_form'
                        ]
                    ]
                ]
            ],
            -100
        );
        $this->buttonList->update('save', 'label', __('Save category store'));
    }

    /**
     * Get header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        $blogComment = $this->coreRegistry->registry('current_megamenu_store');
        if ($blogComment->getId()) {
            return __("Edit Root category #%1", $blogComment->getName());
        }
        return __('New root menu');
    }
}