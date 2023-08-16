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

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;

class View extends Container
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * Edit constructor.
     * @param Registry $coreRegistry
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Registry $coreRegistry,
        Context $context,
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
        $this->buttonList->remove('save');
        $this->buttonList->remove('reset');
        $this->buttonList->remove('delete');
        $this->buttonList->update('back', 'label', __('Close this'));
        $this->buttonList->add(
            'delete_menu',
            [
                'label' => __('Delete'),
                'onclick' => 'window.location.href=\'' . $this->getDeleteUrl() . '\'',
                'class' => 'delete'
            ]
        );
        $this->buttonList->add(
            'edit_config',
            [
                'label' => __('Edit Configuration'),
                'onclick' => 'window.location.href=\'' . $this->getEditConfigUrl() . '\'',
                'class' => 'delete'
            ]
        );
    }

    /**
     * Get Header Text
     *
     * @return Phrase
     */
    public function getHeaderText()
    {
        $blogComment = $this->coreRegistry->registry('current_megamenu_store');
        if ($blogComment->getId()) {
            return __("Edit Root category #%1", $blogComment->getName());
        }
        return __('New root menu');
    }

    /**
     * Get Delete Url
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl(
            'megamenu/category/delete',
            ['id' => $this->getRequest()->getParam('id')]
        );
    }

    public function getEditConfigUrl()
    {
        return $this->getUrl(
            'megamenu/category/config',
            ['id' => $this->getRequest()->getParam('id')]
        );
    }
}
