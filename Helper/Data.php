<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\OptionCustomTricks\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\ScopeInterface;

/**
 * Helper for retrieving custom options settings.
 */
class Data extends AbstractHelper
{
    /**
     * XML path to the configuration setting that determines whether
     * custom options and values should be collapsed by default in the admin panel.
     */
    public const XML_PATH_COLLAPSE_CUSTOM_OPTIONS =
        'mageworx_apo/option_tricks/collapse_option_and_values_by_default';

    public const XML_PATH_OPTIONS_PAGE_SIZE =
        'mageworx_apo/option_tricks/options_page_size';

    public const XML_PATH_VALUES_PAGE_SIZE =
        'mageworx_apo/option_tricks/values_page_size';

    /**
     * XML path for the flag that enables drag-and-drop reorder UI on the
     * custom-options grid in the admin product form. Default is enabled to
     * preserve historical Magento_Ui behaviour; admins are expected to turn
     * it off on stores with very large option sets, where initialising a
     * DnD instance per row dominates initial render time.
     */
    public const XML_PATH_OPTIONS_DND_ENABLED =
        'mageworx_apo/option_tricks/enable_options_dnd';

    /**
     * XML path for the flag that enables the "Import Options" workflow on
     * the custom-options grid in the admin product form. Default is enabled
     * to preserve stock Magento_Catalog behaviour. When disabled, the
     * "Import Options" button is removed from the header and the imports
     * wiring between the options grid and the product_custom_options_listing
     * data provider is unwired — i.e. the import flow is fully cut. This is
     * the recommended setting on stores with very large catalogs/option
     * sets, where loading the listing source data is itself an N+1 query
     * fanout that admins seldom actually use.
     */
    public const XML_PATH_OPTIONS_IMPORT_ENABLED =
        'mageworx_apo/option_tricks/enable_options_import';

    public const DEFAULT_OPTIONS_PAGE_SIZE = 20;
    public const DEFAULT_VALUES_PAGE_SIZE  = 20;
    public const CLEAR_LOCAL_STORAGE_FLAG  = 'mageworx_apo_clear_local_storage';

    /**
     * Checks if custom options dynamic rows should be collapsed by default in the admin panel.
     *
     * @param int|null $storeId Store ID (null for default scope)
     * @return bool True if options should be collapsed, false otherwise.
     * @throws LocalizedException
     */
    public function isOptionsStateCollapsed(?int $storeId = null): bool
    {
        $configValue = $this->scopeConfig->getValue(
            self::XML_PATH_COLLAPSE_CUSTOM_OPTIONS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return filter_var($configValue, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Get number of custom options per page from config
     *
     * @param int|null $storeId
     * @return int
     */
    public function getOptionsPageSize(?int $storeId = null): int
    {
        return (int)($this->scopeConfig->getValue(
            self::XML_PATH_OPTIONS_PAGE_SIZE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        ) ?: self::DEFAULT_OPTIONS_PAGE_SIZE);
    }

    /**
     * Get number of values per page from config
     *
     * @param int|null $storeId
     * @return int
     */
    public function getValuesPageSize(?int $storeId = null): int
    {
        return (int)($this->scopeConfig->getValue(
            self::XML_PATH_VALUES_PAGE_SIZE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        ) ?: self::DEFAULT_VALUES_PAGE_SIZE);
    }

    /**
     * Whether the admin custom-options grid should keep its drag-and-drop
     * reorder UI. Disabling DnD removes a per-row UI component initialisation
     * that dominates initial render time on products with hundreds/thousands
     * of options.
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isOptionsDndEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_OPTIONS_DND_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Whether the admin custom-options grid should keep the "Import Options"
     * workflow. Disabling cuts the wiring between the options grid and the
     * product_custom_options_listing data provider, removes the import
     * button from the header, and avoids loading the (often N+1-fanout)
     * source listing data on initial render.
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isOptionsImportEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_OPTIONS_IMPORT_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
