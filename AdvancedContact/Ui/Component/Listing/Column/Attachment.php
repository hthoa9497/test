<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_AdvancedContact
 * @copyright Copyright (C) 2020 Magezon (https://www.magezon.com)
 */

namespace Magezon\AdvancedContact\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magezon\AdvancedContact\Model\Contact;
use Magento\Framework\Filesystem;
use Magento\Store\Model\StoreManagerInterface;

class Attachment extends Column
{
    const URL_PATH_DELETE = 'advancedcontactform/contact/delete';

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;
    
    protected $fileSystem;
    
    protected $storeManager;

    /**
     * IconActions constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        Filesystem $filesystem,
        StoreManagerInterface $storeManager,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlBuilder = $urlBuilder;
        $this->fileSystem = $filesystem;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritDoc
     */
    public function prepareDataSource(array $dataSource)
    {
        $store = $this->storeManager->getStore();
        $mediaPath = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $path = $mediaPath . Contact::ATTACHMENT_UPLOAD_DIR;
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if ($item['attachment']) {
                    $item['attachment'] = html_entity_decode('<a href="' . $path . $item['attachment'] . '" target="_blank"> Attachment </a>');
                }
            }
        }

        return $dataSource;
    }
}
