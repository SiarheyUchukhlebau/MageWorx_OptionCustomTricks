<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\OptionCustomTricks\Plugin;

use Magento\Catalog\Ui\DataProvider\Product\Form\ProductDataProvider;
use MageWorx\OptionCustomTricks\Helper\Data as OptionCustomTricksHelper;

class ProductDataProviderPlugin
{
    private OptionCustomTricksHelper $helper;

    public function __construct(
        OptionCustomTricksHelper $helper
    ) {
        $this->helper = $helper;
    }

    public function afterGetMeta(ProductDataProvider $subject, array $result): array
    {
        $groupCustomOptionsName = 'custom_options';
        $optionContainerName    = 'record';

        if (!isset($result[$groupCustomOptionsName]['children']['options'])) {
            return $result;
        }

        $options = &$result[$groupCustomOptionsName]['children']['options'];

        if (!isset($options['children'][$optionContainerName]['children'])) {
            return $result;
        }

        $options['arguments']['data']['config']['pageSize'] = $this->helper->getOptionsPageSize();

        $isDndEnabled = $this->helper->isOptionsDndEnabled();
        if (!$isDndEnabled) {
            $options['arguments']['data']['config']['dndConfig'] = ['enabled' => false];
        }

        if (!$this->helper->isOptionsImportEnabled()) {
            // Drop wiring to product_custom_options_listing data provider — it
            // pre-loads source-product options via N+1 queries even when the
            // admin never clicks "Import".
            unset($options['arguments']['data']['config']['imports']);

            // Magento_Catalog uses CONTAINER_HEADER_NAME = 'container_header'.
            if (isset($result[$groupCustomOptionsName]['children']['container_header']['children']['button_import'])) {
                unset($result[$groupCustomOptionsName]['children']['container_header']['children']['button_import']);
            }
        }

        $valuesPageSize = $this->helper->getValuesPageSize();
        $isCollapsed    = $this->helper->isOptionsStateCollapsed();
        $record         = &$options['children'][$optionContainerName];

        if (isset($record['children'])) {
            foreach ($record['children'] as &$option) {
                if ($isCollapsed) {
                    $option['arguments']['data']['config']['collapsible'] = true;
                    $option['arguments']['data']['config']['opened']      = false;
                }
                $option['children']['values']['arguments']['data']['config']['pageSize'] = $valuesPageSize;
                if (!$isDndEnabled) {
                    $option['children']['values']['arguments']['data']['config']['dndConfig'] = ['enabled' => false];
                }
            }
        }

        return $result;
    }
}
