<?php
/**
 * Copyright © 2016 Rouven Alexander Rieker
 * See LICENSE.md bundled with this module for license details.
 */
namespace Semaio\ConfigImportExport\Model\Converter;

use Magento\Store\Model\StoreManagerInterface;

/**
 * Class ScopeConverter
 *
 * @package Semaio\ConfigImportExport\Model\Converter
 */
class ScopeConverter implements ScopeConverterInterface
{
    const SCOPE_STORES = 'stores';
    const SCOPE_WEBSITES = 'websites';
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var array
     */
    private $entityStore;

    /**
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
    }

    /**
     * Converts a string scope to integer scope id if needed
     *
     * @param string|int   $scopeId Scope ID
     * @param string|mixed $scope   Scope
     * @return int
     */
    public function convert($scopeId, $scope)
    {
        if (is_numeric($scopeId)) {
            return $scopeId;
        }

        $entities = $this->getEntityStore($scope);
        if (isset($entities[$scopeId])) {
            return $entities[$scopeId]->getId();
        }

        throw new ScopeConvertException(sprintf('Unable to process code "%s" for scope "%s"', $scopeId, $scope));
    }

    /**
     * @param $scope
     *
     * @return \Magento\Store\Api\Data\WebsiteInterface[]|\Magento\Store\Api\Data\StoreInterface[]
     */
    private function getEntityStore($scope)
    {
        if (isset($this->entityStore[$scope])) {
            return $this->entityStore[$scope];
        }

        switch ($scope)
        {
            case self::SCOPE_STORES:
                $this->entityStore[$scope] = $this->storeManager->getStores(true, true);
                break;

            case self::SCOPE_WEBSITES:
                $this->entityStore[$scope] = $this->storeManager->getWebsites(true, true);
                break;

            default:
                throw new ScopeConvertException(sprintf('Unknown scope "%s"', $scope));
        }

        return $this->entityStore[$scope];
    }
}
