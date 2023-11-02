<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\OptionAdvancedPricing\Plugin;

use MageWorx\OptionAdvancedPricing\Helper\Data as Helper;
use MageWorx\OptionAdvancedPricing\Model\SpecialPrice as SpecialPriceModel;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use MageWorx\OptionBase\Helper\Price as BasePriceHelper;
use Magento\Framework\Json\DecoderInterface;
use MageWorx\OptionBase\Plugin\ExtendPriceConfig;
use Magento\Catalog\Model\Product\Option;
use Magento\Catalog\Model\Product\Option\Value;

class AfterExtendPriceConfig
{
    protected Helper $helper;
    protected SpecialPriceModel $specialPriceModel;
    protected PriceCurrencyInterface $priceCurrency;
    protected BasePriceHelper $basePriceHelper;
    protected DecoderInterface $jsonDecoder;

    public function __construct(
        Helper $helper,
        SpecialPriceModel $specialPriceModel,
        PriceCurrencyInterface $priceCurrency,
        BasePriceHelper $basePriceHelper,
        array $data = []
    ) {
        $this->helper            = $helper;
        $this->specialPriceModel = $specialPriceModel;
        $this->priceCurrency     = $priceCurrency;
        $this->basePriceHelper   = $basePriceHelper;
    }

    public function afterGetExtendedOptionValueJsonConfig(
        ExtendPriceConfig $subject,
        array $result,
        array $defaultConfig,
        Option $option,
        int $valueId,
        Value $value
    ): array {
        if (!$this->helper->isSpecialPriceEnabled()) {
            return $result;
        }

        $product        = $subject->getProduct();
        $specialPrice   = $this->specialPriceModel->getActualSpecialPrice($value, true);
        $needIncludeTax = $this->basePriceHelper->getCatalogPriceContainsTax($product->getStoreId());
        $isSpecialPrice = false;

        if ($specialPrice !== null) {
            $basePriceAmount  = $result['prices']['basePrice']['amount'];
            $finalPriceAmount = $result['prices']['finalPrice']['amount'];
            if ($needIncludeTax) {
                $basePriceAmount = min(
                    $basePriceAmount,
                    $specialPrice * ($basePriceAmount / $finalPriceAmount)
                );
            } else {
                $basePriceAmount = min($basePriceAmount, $specialPrice);
            }
            $finalPriceAmount = min($finalPriceAmount, $specialPrice);

            if ($specialPrice <= $finalPriceAmount) {
                $isSpecialPrice = true;
            }

            $basePriceAmount  = $this->basePriceHelper->getTaxPrice(
                $product,
                $basePriceAmount,
                $needIncludeTax
            );
            $finalPriceAmount = $this->basePriceHelper->getTaxPrice(
                $product,
                $finalPriceAmount,
                $needIncludeTax || $isSpecialPrice
            );

            $result['prices']['basePrice']['amount']  = $basePriceAmount;
            $result['prices']['finalPrice']['amount'] = $finalPriceAmount;
        }

        if ($specialPrice !== null) {
            $result['special_price_display_node'] = $this->helper->getSpecialPriceDisplayNode(
                $result['prices'],
                $this->specialPriceModel->getSpecialPriceItem()
            );
        };

        return $result;
    }
}
