<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\OptionCustomTricks\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    public const XML_PATH_COLLAPSE_CUSTOM_OPTIONS =
        'mageworx_apo/option_tricks/collapse_option_and_values_by_default';

    public const XML_PATH_OPTIONS_PAGE_SIZE =
        'mageworx_apo/option_tricks/options_page_size';

    public const XML_PATH_VALUES_PAGE_SIZE =
        'mageworx_apo/option_tricks/values_page_size';

    public const XML_PATH_OPTIONS_DND_ENABLED =
        'mageworx_apo/option_tricks/enable_options_dnd';

    public const XML_PATH_OPTIONS_IMPORT_ENABLED =
        'mageworx_apo/option_tricks/enable_options_import';

    public const DEFAULT_OPTIONS_PAGE_SIZE = 10;
    public const DEFAULT_VALUES_PAGE_SIZE  = 10;
    public const CLEAR_LOCAL_STORAGE_FLAG  = 'mageworx_apo_clear_local_storage';

    public function isOptionsStateCollapsed(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_COLLAPSE_CUSTOM_OPTIONS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getOptionsPageSize(?int $storeId = null): int
    {
        return (int)($this->scopeConfig->getValue(
            self::XML_PATH_OPTIONS_PAGE_SIZE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        ) ?: self::DEFAULT_OPTIONS_PAGE_SIZE);
    }

    public function getValuesPageSize(?int $storeId = null): int
    {
        return (int)($this->scopeConfig->getValue(
            self::XML_PATH_VALUES_PAGE_SIZE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        ) ?: self::DEFAULT_VALUES_PAGE_SIZE);
    }

    public function isOptionsDndEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_OPTIONS_DND_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function isOptionsImportEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_OPTIONS_IMPORT_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
