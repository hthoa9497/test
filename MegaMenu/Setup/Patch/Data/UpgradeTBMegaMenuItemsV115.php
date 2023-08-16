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
 * @package    Bss_B2bRegistration
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2022 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
declare(strict_types=1);

namespace Bss\MegaMenu\Setup\Patch\Data;

use Bss\MegaMenu\Model\ConfigFactory;
use Bss\MegaMenu\Model\MenuStoresFactory;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Data patch format
 */
class UpgradeTBMegaMenuItemsV115 implements DataPatchInterface
{
    /**
     * @var ConfigFactory
     */
    protected $configFactory;

    /**
     * @var MenuStoresFactory
     */
    protected $menuStore;

    /**
     * UpgradeData table bss_megamenu_stores constructor.
     *
     * @param ConfigFactory $configFactory
     * @param MenuStoresFactory $menuStore
     */
    public function __construct(
        ConfigFactory $configFactory,
        MenuStoresFactory $menuStore
    ) {
        $this->configFactory = $configFactory;
        $this->menuStore = $menuStore;
    }

    /**
     * Upgrade table customer_form_attribute.
     *
     * @return void
     */
    public function apply()
    {
        $modelFactory = $this->menuStore->create();
        $configFactory = $this->configFactory->create();
        $configCollection = $configFactory->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('path', 'megamenu/tree/data');
        $configData = $configCollection->getData();
        if ($configData) {
            foreach ($configData as $data) {
                $modelCollection = $modelFactory->getCollection()
                    ->addFieldToSelect('*')
                    ->addFieldToFilter('store_id', $data['scope_id'])
                    ->addFieldToFilter('priority', ['null' => true])
                    ->addFieldToFilter('value', '');
                if ($modelCollection->getData()) {
                    foreach ($modelCollection as $value) {
                        $value->setValue($data['value']);
                    }
                    $modelCollection->save();
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * Compare ver module.
     *
     * @return string
     */
    public static function getVersion()
    {
        return '1.1.5';
    }
}
