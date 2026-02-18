<?php

namespace WHMCS\Cloud4Africa\Repository;

interface WhmcsRepositoryInterface
{
    
    /**
     * @return array<int, \stdClass>
     */
    public function getRawProducts(): array;

    /**
     * @return array<int, \stdClass>
     */
    public function getRawProduct(int $productId): array;

    public function countClientProductsByServerType(int $clientId, string $serverType): int;

    /**
     * @return array<int, \stdClass>
     */
    public function getClientProductsByServerType(int $clientId, string $serverType): array;

    /**
     * @return array<int, \stdClass>
     */
    public function getClientProductByServerType(int $clientId, int $productId, string $serverType): array;

    public function countClientProductsBySlug(int $clientId, string $slug): int;

    /**
     * @return array<int, \stdClass>
     */
    public function getClientProductsBySlug(int $clientId, string $slug): array;

    /**
     * @return array<int, \stdClass>
     */
    public function getClientProductBySlug(int $clientId, int $hostingId, string $slug): array;

    /**
     * @return array<int, \stdClass>
     */
    public function getClientProductCustomFieldData(int $productId): array;

    public function countClientProductAddons(int $clientId): int;

    /**
     * @return array<int, \stdClass>
     */
    public function getClientProductAddons(int $clientId): array;

    /**
     * @return array<int, \stdClass>
     */
    public function getClientProductAddon(int $clientId, int $hostingAddonId, string $slug): array;

    /**
     * @return array<int, \stdClass>
     */
    public function getClientProductAddonCustomFieldData(int $productId): array;

    /**
     * @return array<int, \stdClass>
     */
    public function getCustomFieldValuesByType(string $type): array;

    /**
     * @return array<int, \stdClass>
     */
    public function getCustomFieldValuesByTypeAndFieldName(string $type, string $fieldName): array;

    /**
     * @return array<int, \stdClass>
     */
    public function getClientProductRegion(int $productId): array;

    /**
     * @return array<int, \stdClass>
     */
    public function getClientDomains(int $clientId): array;

    /**
     * @return array<int, \stdClass>
     */
    public function getClientDomain(int $clientId, string $domainName): ?array

    /**
     * @return array<int, \stdClass>
     */
    public function getClientActiveDomains(int $clientId): array;

    /**
     * @return array<int, \stdClass>
     */
    public function getClientPendingDomains(int $clientId): array;

    /**
     * @return array<int, \stdClass>
     */
    public function getClientExpiredDomains(int $clientId): array;

    /**
     * @return array<int, \stdClass>
     */
    public function getClientProducts(int $clientId): array;

    /**
     * @return array<int, \stdClass>
     */
    public function getClientProductById(int $clientId, int $productId): array;

    /**
     * @return array<int, \stdClass>
     */
    public function getClientProductByServiceId(int $clientId, int $serviceId): array;

    /**
     * @return array<int, \stdClass>
     */
    public function getClientProductByDomain(int $clientId, string $domain): array;

    /**
     * @return array<int, \stdClass>
     */
    public function getClientDetails(int $clientId): array;

    /**
     * @return array<int, \stdClass>
     */
    public function getDomainWhois(string $domain): array;
}
