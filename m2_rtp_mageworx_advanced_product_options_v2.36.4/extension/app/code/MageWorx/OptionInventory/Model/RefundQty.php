<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OptionInventory\Model;

use \Magento\Catalog\Model\ResourceModel\Product\Option\Value\CollectionFactory as ValueCollection;
use \Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Store\Model\Store;
use MageWorx\OptionBase\Helper\Data as BaseHelper;
use MageWorx\OptionInventory\Helper\Stock as HelperStock;

/**
 * Class RefundQty. Refund option values qty when order is cancel or credit memo.
 */
class RefundQty
{
    protected ValueCollection $valueCollection;
    protected StockRegistryInterface $stockRegistry;
    protected HelperStock $helperStock;
    protected BaseHelper $baseHelper;

    /**
     * RefundQty constructor.
     *
     * @param ValueCollection $valueCollection
     */
    public function __construct(
        ValueCollection $valueCollection,
        StockRegistryInterface $stockRegistry,
        HelperStock $helperStock,
        BaseHelper $baseHelper
    ) {
        $this->valueCollection = $valueCollection;
        $this->stockRegistry   = $stockRegistry;
        $this->helperStock     = $helperStock;
        $this->baseHelper      = $baseHelper;
    }

    /**
     * Refund qty when order is cancele or credit memo.
     * Walk through the all order $items, find count qty to refund by the $qtyFieldName
     * and refund it for all option values in this order.
     *
     * @param array $items
     * @param string $qtyFieldName
     * @return $this
     */
    public function refund(array $items, string $qtyFieldName): RefundQty
    {
        foreach ($items as $item) {
            $qty = (float)$item->getQty();
            if (!$qty) {
                continue;
            }

            $itemData       = $item->getData();
            $infoBuyRequest = $itemData['product_options']['info_buyRequest'];

            if (!isset($infoBuyRequest['options'])) {
                continue;
            }

            if (!isset($itemData['product_options']['options'])) {
                continue;
            }

            $optionsData = [];
            foreach ($itemData['product_options']['options'] as $optionData) {
                $optionsData[$optionData['option_id']] = $optionData;
            }

            if ($qtyFieldName == 'qty_refunded') {
                $orderItemQtyReturned = $qty + $itemData['qty_invoiced'] - $itemData[$qtyFieldName];
            } else {
                $orderItemQtyReturned = $itemData[$qtyFieldName];
            }

            if (!$orderItemQtyReturned) {
                continue;
            }

            $itemOptions          = $infoBuyRequest['options'];
            $valueIds             = [];
            foreach ($itemOptions as $optionId => $value) {
                if (!$this->baseHelper->isSelectableOption($optionsData[$optionId]['option_type'])) {
                    continue;
                }

                if (is_array($value)) {
                    foreach ($value as $valueId) {
                        $valueIds[] = $valueId;
                    }
                } else {
                    $valueIds[] = $value;
                }
            }

            $valuesCollection = $this->valueCollection
                ->create()
                ->addPriceToResult(Store::DEFAULT_STORE_ID)
                ->getValuesByOption($valueIds)
                ->load();

            if (!$valuesCollection->getSize()) {
                continue;
            }

            $valuesCollectionItems = $valuesCollection->getItems();
            foreach ($valueIds as $valueId) {
                if (!isset($optionsData[$optionId]) || 
                    !$this->baseHelper->isSelectableOption($optionsData[$optionId]['option_type'])
                ) {
                    continue;
                }

                $valueModel = $valuesCollectionItems[$valueId];

                if (!$valueModel) {
                    continue;
                }

                $valueModel->setStoreId(Store::DEFAULT_STORE_ID);

                if (!$valueModel->getManageStock()) {
                    continue;
                }

                $totalQtyReturned = $this->getTotalQtyReturned($valueModel, $infoBuyRequest, $orderItemQtyReturned);
                $resultQty        = $valueModel->getQty() + $totalQtyReturned;
                $valueSku         = $valueModel->getSku();
                $valueModel->setQty($resultQty);

                if (!$this->helperStock->validateLinkedQtyField()) {
                    continue;
                }

                if ($valueModel->getSkuIsValid()) {
                    $this->updateLinkedProductStock($valueSku, (float)$resultQty);
                }
            }

            $valuesCollection->save();
        }

        return $this;
    }

    /**
     * Calculates and return total qty considering QtyInput of order item
     *
     * @param \Magento\Framework\DataObject $valueModel
     * @param array $infoBuyRequest
     * @param int $orderItemQtyReturned
     * @return int
     */
    public function getTotalQtyReturned($valueModel, $infoBuyRequest, $orderItemQtyReturned)
    {
        $optionId = $valueModel->getOptionId();
        $valueId  = $valueModel->getOptionTypeId();
        if (empty($infoBuyRequest['options_qty']) || empty($infoBuyRequest['options_qty'][$optionId])) {
            return $orderItemQtyReturned;
        }

        $valueQty = 1;
        if (!empty($infoBuyRequest['options_qty'][$optionId][$valueId])) {
            $valueQty = $infoBuyRequest['options_qty'][$optionId][$valueId];
        } elseif (!is_array($infoBuyRequest['options_qty'][$optionId])) {
            $valueQty = $infoBuyRequest['options_qty'][$optionId];
        }

        return $valueQty * $orderItemQtyReturned;
    }

    /**
     * @inheritdoc
     */
    public function updateLinkedProductStock(string $sku, float $qty): void
    {
        $stockItem = $this->stockRegistry->getStockItemBySku($sku);
        $stockItem->setQty($qty);
        $this->stockRegistry->updateStockItemBySku($sku, $stockItem);
    }
}
