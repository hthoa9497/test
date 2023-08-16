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
namespace Bss\MegaMenu\Helper;

use Bss\MegaMenu\Model\MenuStoresFactory;
use Magento\Framework\App\State;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $serializer;

    /**
     * @var MenuStoresFactory
     */
    protected $menuStoresFactory;

    /**
     * @var State
     */
    protected $state;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Request\Http $request

     * @param MenuStoresFactory $menuStoresFactory
     * @param State $state
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        MenuStoresFactory $menuStoresFactory,
        State $state
    ) {
        $this->scopeConfig = $context->getScopeConfig();
        $this->request = $request;
        $this->categoryRepository = $categoryRepository;
        $this->storeManager = $storeManager;
        $this->serializer = $serializer;
        $this->menuStoresFactory = $menuStoresFactory;
        $this->state = $state;
        parent::__construct($context);
    }

    /**
     * @param string $config
     * @return bool|mixed
     */
    public function getConfig($config = '')
    {
        if ($config == '') {
            return false;
        }
        return $this->scopeConfig->getValue(
            'megamenu/general/' . $config,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return bool
     */
    public function isHomeUrl()
    {
        if ($this->request->getFullActionName() == 'cms_index_index') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param array $menu
     * @return string
     */
    public function getCustomCss($menu)
    {
        $customCss = '';
        if (isset($menu['custom_css']) && $menu['custom_css'] != '') {
            $customCss = $menu['custom_css'];
        }
        return $customCss;
    }

    /**
     * @param object $page
     * @return string
     */
    public function getPageUrl($page)
    {
        return $this->_urlBuilder->getUrl(null, ['_direct' => $page->getIdentifier()]);
    }

    /**
     * @param array $menu
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getLinkUrl($menu)
    {
        $link = '#';
        switch ($menu['url_type']) {
            case 1:
                if ($menu['custom_link'] != '') {
                    $link = $this->storeManager->getStore()->getUrl($menu['custom_link']);
                }
                break;

            case 0:
                if ($menu['category_id'] != '' && $menu['category_id'] > 0) {
                    try {
                        $category = $this->categoryRepository
                            ->get($menu['category_id'], $this->storeManager->getStore()->getId());
                        $link = $category->getUrl();
                    } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
                        $this->_logger->critical($exception);
                        break;
                    }
                }
        }
        return $link;
    }

    /**
     * @param string $label
     * @return string
     */
    public function getLabelColor($label)
    {
        $html = '';
        switch ($label) {
            case 'new':
                $html = '&nbsp;&nbsp;<span class="label label-info">' . __("New") . '</span>';
                break;

            case 'hot':
                $html = '&nbsp;&nbsp;<span class="label label-danger">' . __("Hot") . '</span>';
                break;

            case 'sale':
                $html = '&nbsp;&nbsp;<span class="label label-success">' . __("Sales") . '</span>';
        }
        return $html;
    }

    /**
     * @param object $menu
     * @return float|int
     */
    public function checkSize($menu)
    {
        $size = 1;
        if ($menu['block_right'] != '') {
            $size++;
        }
        if ($menu['block_left'] != '') {
            $size++;
        }
        return 12 / $size;
    }

    /**
     * @param null $storeId
     * @return mixed|string
     */
    public function getMegaMenuConfig($storeId = null)
    {
        try {
            if ($storeId == null) {
                $storeId = 0;
            }
            $menu = null;
            $menuFactory = $this->menuStoresFactory->create();
            if ($this->state->getAreaCode() === 'adminhtml') {
                $menu = $this->getDataBE($menuFactory, $storeId, 'store_id');
            } else {
                $data = $this->getDataFE($menuFactory, $storeId);
                if ($data->getData()) {
                    if (count($data->getData()) == 1) {
                        $menu = $data->getData()[0]['value'];
                    } else {
                        $maxPri = max(array_column($data->getData(), 'priority'));
                        $format = $data->getData();
                        if ($maxPri > 0) {
                            foreach ($format as $key => $value) {
                                if ($value['priority'] == 0) {
                                    unset($format[$key]);
                                }
                            }
                        }
                        $priAc = min(array_column($format, 'priority'));
                        $laterMenu = max(array_column($format, 'category_store_id'));
                        foreach ($format as $value) {
                            if ($value['value'] && $value['priority'] > 0
                                && $value['priority'] == $priAc) {
                                return $value['value'];
                            } else {
                                if ($value['category_store_id'] == $laterMenu) {
                                    return $value['value'];
                                }
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $menu = false;
        }
        return $menu;
    }

    /**
     * @param array $data
     * @return string
     */
    public function serialize($data)
    {
        return $this->serializer->serialize($data);
    }

    /**
     * @param string $string
     * @return mixed
     */
    public function unserialize($string)
    {
        return $this->serializer->unserialize($string);
    }

    /**
     * Get Data Render Menu for BackEnd
     *
     * @param mixed|array $menuFactory
     * @param string $id
     * @param string|null $key
     * @return mixed
     */
    public function getDataBE($menuFactory, $id, $key = null)
    {
        $storeModel = $menuFactory->load($id, $key);
        return $storeModel->getValue();
    }

    /**
     * Get Data Render Menu for FrontEnd
     *
     * @param mixed|array $menuFactory
     * @param string $storeId
     * @return mixed
     */
    public function getDataFE($menuFactory, $storeId)
    {
        return $menuFactory->getCollection()->addFieldToFilter('store_id', [
            ['like' =>  $storeId . ',%'],
            ['like' => '%,' . $storeId . ',%'],
            ['like' => '%,' . $storeId ],
            ['eq' =>  $storeId ],
            ['eq' =>  0]
        ]);
    }
}
