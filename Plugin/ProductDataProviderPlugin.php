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
    /**
     * @var OptionCustomTricksHelper
     */
    private OptionCustomTricksHelper $helper;

    /**
     * @param OptionCustomTricksHelper $helper
     */
    public function __construct(
        OptionCustomTricksHelper $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Modify UI meta data for custom options:
     * - Collapse options if enabled in admin settings
     * - Set custom page size for options and values pagination
     *
     * @param ProductDataProvider $subject
     * @param array $result
     * @return array
     */
    public function afterGetMeta(ProductDataProvider $subject, array $result): array
    {
        $groupCustomOptionsName = 'custom_options';
        $optionContainerName    = 'record';

        $optionsPageSize = $this->helper->getOptionsPageSize();
        $valuesPageSize  = $this->helper->getValuesPageSize();

        if (!isset($result[$groupCustomOptionsName]['children']['options'])) {
            return $result;
        }

        // name = product_form.product_form.custom_options.options
        $options = &$result[$groupCustomOptionsName]['children']['options'];

        if (!isset($options['children'][$optionContainerName]['children'])) {
            return $result;
        }

        // Set page size for options dynamic rows
        $options['arguments']['data']['config']['pageSize'] = $optionsPageSize;

        // Optionally disable drag-and-drop on the options grid. Driven by
        // the admin setting `mageworx_apo/option_tricks/enable_options_dnd`.
        // On large products (hundreds/thousands of options) Magento_Ui
        // dynamic-rows initialises a DnD UI component per row, which
        // dominates the initial render budget; admins are expected to
        // disable DnD in those scenarios and use the Sort Order column
        // instead. Scoped to the custom-options grid only.
        $isDndEnabled = $this->helper->isOptionsDndEnabled();
        if (!$isDndEnabled) {
            $options['arguments']['data']['config']['dndConfig'] = ['enabled' => false];
        }

        $record = &$options['children'][$optionContainerName];

        if (isset($record['children'])) {
            foreach ($record['children'] as &$option) {
                // Collapse custom options by modifying the meta configuration if enabled in admin settings.
                if ($this->helper->isOptionsStateCollapsed()) {
                    $option['arguments']['data']['config']['collapsible'] = true;
                    $option['arguments']['data']['config']['opened']      = false;
                }
                // Set page size for values dynamic rows
                $option['children']['values']['arguments']['data']['config']['pageSize'] = $valuesPageSize;
                // Disable DnD for values grid for the same reason as options above.
                if (!$isDndEnabled) {
                    $option['children']['values']['arguments']['data']['config']['dndConfig'] = ['enabled' => false];
                }
            }
        }

        return $result;
    }
}
