<?php

namespace WHMCS\Cloud4Africa\Repository;

interface WhmcsRepositoryInterface
{
    /**
     * @param mixed $id
     * @param string $table
     * @return object|NULL
     */
    public function find(mixed $id, ?string $table = null): ?object;
    
    /**
     * @param array<string, mixed> $criteria
     * @param string $table
     * @return object|NULL
     */
    public function findOneBy(array $criteria = [], ?string $table = null): ?object;
    
    /**
     * @param array<string, mixed> $criteria
     * @param string $table
     * @return array
     */
    public function findBy(array $criteria = [], ?string $table = null): array;
    
    /**
     * @param string $column
     * @param array $criteria
     * @param string $table
     * @return array
     */
    public function findColumn(string $column, ?array $criteria = [], ?string $table = null): array;
    
    /**
     * @param string $table
     * @return array
     */
    public function findAll(?string $table = null): array;
    
    /**
     * @param array<string, mixed> $criteria
     * @param string $table
     * @return int
     */
    public function countBy(array $criteria = [], ?string $table = null): int;
    
    /**
     * @param array<string, mixed> $criteria
     * @param string $table
     * @return bool
     */
    public function existsBy(array $criteria = [], ?string $table = null): bool;
    
    /**
     * @param array<string, mixed> $values
     * @param string $table
     * @return bool
     */
    public function insert(array $values, ?string $table = null): bool;
    
    /**
     * @param array<string, mixed> $values
     * @param mixed $id
     * @return int
     */
    public function update(array $values = [], mixed $id, ?string $table = null): int;
    
    /**
     * @param array<string, mixed> $values
     * @param array<string, mixed> $criteria
     * @return int
     */
    public function updateBy(array $values = [], ?array $criteria = null, ?string $table = null): int;
    
    /**
     * @param mixed $id
     * @return int
     */
    public function delete(mixed $id, ?string $table = null): int;
    
    /**
     * @param array<string, mixed> $criteria
     * @return int
     */
    public function deleteBy(array $criteria = [], ?string $table = null): int;
    
    /**
     * @return array<int, \stdClass>
     */
    public function selectSQL(string $sql, array $parameters = []): array;
    
    /**
     * @return int
     */
    public function countSQL(string $sql, array $parameters = []): int;
    
    /**
     * @return bool
     */
    public function existsSQL(string $sql, array $parameters = []): bool;
    
    /**
     * @return void
     */
    public function insertSQL(string $sql, array $parameters = []): void;
    
    /**
     * @return void
     */
    public function updateSQL(string $sql, array $parameters = []): void;
    
    /**
     * @return void
     */
    public function deleteSQL(string $sql, array $parameters = []): void;
    
    /**
     * @return array<int, \stdClass>
     */
    public function findValidKarajanToken(): array;
    
    /**
     * @return array<int, \stdClass>
     */
    public function findKarajanServer(string $serverType = 'karajan'): array;
    
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
    public function getClientProductAddon(int $clientId, int $hostingAddonId): array;
    
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
    public function getClientDomain(int $clientId, string $domainName): ?array;
    
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
     *
     * @param int $clientId
     * @param int $serviceId
     * @return array<string, mixed>
     */
    public function getClientProductByServiceId(int $clientId, int $serviceId): array;
    
    /**
     * @return array<int, \stdClass>
     */
    public function getClientProductByDomain(int $clientId, string $domain): ?array;
    
    /**
     * @return array<int, \stdClass>
     */
    public function getClientDetails(int $clientId): array;
    
    /**
     * @return array<int, \stdClass>
     */
    public function getDomainWhois(string $domain): array;
    
    /**
     * @return array<string, \stdClass>
     */
    public function getDecryptedPassword($password): array;
}
