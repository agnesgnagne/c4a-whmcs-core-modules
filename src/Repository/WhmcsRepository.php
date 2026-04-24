<?php

namespace WHMCS\Cloud4Africa\Repository;

use WHMCS\Cloud4Africa\Service\WhmcsLocalApiInterface;
use Illuminate\Database\Capsule\Manager as Capsule;

class WhmcsRepository implements WhmcsRepositoryInterface
{
    /**
     * @var \WHMCS\Cloud4Africa\Service\WhmcsLocalApiInterface
     */
    private WhmcsLocalApiInterface $api;
    
    /**
     * @var \Illuminate\Database\Capsule\Manager
     */
    private Capsule $capsule;
    
    /**
     * @var string
     */
    private string $tableName;
    
    public function __construct(WhmcsLocalApiInterface $api, Capsule $capsule, ?string $tableName = null)
    {
        $this->api = $api;
        $this->capsule = $capsule;
        $this->tableName = $tableName ?? '';
    }
    
    /**
     * @param mixed $id
     * @return object|NULL
     */
    public function find(mixed $id, ?string $table = null): ?object
    {
        return $this->capsule
        ->connection()
        ->table($table ?? $this->tableName)
        ->where('id', $id);
    }
    
    /**
     * @param array $criteria
     * @param string $table
     * @return object|NULL
     */
    public function findOneBy(array $criteria = [], ?string $table = null): ?object
    {
        $query = $this->capsule->connection()->table($table ?? $this->tableName);
        
        foreach ($criteria as $key => $value) {
            if (is_array($value)) {
                $query->where($key, $value['operator'], $value['value']);
            } else {
                $query->where($key, $value);
            }
        }
        
        return $query->first();
    }
    
    /**
     * @param array $criteria
     * @param string $table
     * @return array
     */
    public function findBy(array $criteria = [], ?string $table = null): array
    {
        $query = $this->capsule->connection()->table($table ?? $this->tableName);
        
        foreach ($criteria as $key => $value) {
            if (is_array($value)) {
                $query->where($key, $value['operator'], $value['value']);
            } else {
                $query->where($key, $value);
            }
        }
        
        return $query->get()->toArray();
    }
    
    /**
     * @param string $column
     * @param array $criteria
     * @param string $table
     * @return array
     */
    public function findColumn(string $column, ?array $criteria = [], ?string $table = null): array
    {
        $query = $this->capsule->connection()->table($this->tableName);
        
        foreach ($criteria as $key => $value) {
            $query->where($key, $value);
        }
        
        return $query->pluck($column)->toArray();
    }
    
    /**
     * @param string $table
     * @return array
     */
    public function findAll(?string $table = null): array
    {
        return $this->capsule
        ->connection()
        ->table($table ?? $this->tableName)
        ->get()
        ->toArray();
    }
    
    
    /**
     * @param array $criteria
     * @param string $table
     * @return int
     */
    public function countBy(array $criteria = [], ?string $table = null): int
    {
        $query = $this->capsule->connection()->table($table ?? $this->tableName);
        
        foreach ($criteria as $key => $value) {
            if (is_array($value)) {
                $query->where($key, $value[0], $value[1]);
            } else {
                $query->where($key, $value);
            }
        }
        
        return $query->count();
    }
    
    /**
     * @param array $criteria
     * @param string $table
     * @return bool
     */
    public function existsBy(array $criteria = [], ?string $table = null): bool
    {
        if (empty($criteria)) {
            return false;
        }
        
        $query = $this->capsule->connection()->table($table ?? $this->tableName);
        
        foreach ($criteria as $key => $value) {
            if (is_array($value)) {
                $query->where($key, $value[0], $value[1]);
            } else {
                $query->where($key, $value);
            }
        }
        
        return $query->exists();
    }
    
    /**
     * @param array $values
     * @param string $table
     * @return bool
     */
    public function insert(array $values, ?string $table = null): bool
    {
        return $this->capsule->connection()
        ->table($table ?? $this->tableName)
        ->insert($values);
    }
    
    /**
     * @param array $values
     * @param mixed $id
     * @return int
     */
    public function update(array $values = [], mixed $id, ?string $table = null): int
    {
        return $this->capsule
                    ->connection()
                    ->table($table ?? $this->tableName)
                    ->where('id', $id)
                    ->update($values)
        ;
    }
    
    /**
     * @param array $values
     * @param array $criteria
     * @return int
     */
    public function updateBy(array $values = [], ?array $criteria = [], ?string $table = null): int
    {
        $query = $this->capsule->connection()->table($table ?? $this->tableName);
        
        if ($criteria) {
            foreach ($criteria as $key => $value) {
                if (is_array($value)) {
                    $query->where($key, $value[0], $value[1]);
                } else {
                    $query->where($key, $value);
                }
            }
        }
        
        return $query->update($values);
    }
    
    /**
     * @param mixed $id
     * @return int
     */
    public function delete(mixed $id, ?string $table = null): int
    {
        return $this->capsule->connection()
        ->table($table ?? $this->tableName)
        ->where('id', $id)
        ->delete();
    }
    
    /**
     * @param array $criteria
     * @return int
     */
    public function deleteBy(array $criteria = [], ?string $table = null): int
    {
        $query = $this->capsule->connection()->table($table ?? $this->tableName);
        
        if ($criteria) {
            foreach ($criteria as $key => $value) {
                if (is_array($value)) {
                    $query->where($key, $value[0], $value[1]);
                } else {
                    $query->where($key, $value);
                }
            }
        }
        
        return $query->delete();
    }
    
    public function selectSQL(string $sql, array $parameters = []): array
    {
        return $this->capsule->connection()->select($sql, $parameters);
    }
    
    public function countSQL(string $sql, array $parameters = []): int
    {
        $results = $this->capsule->connection()->select($sql, $parameters);
        return (int) ($results[0]->count ?? 0);
    }
    
    public function existsSQL(string $sql, array $parameters = []): bool
    {
        $results = $this->capsule->connection()->select($sql, $parameters);
        return !empty($results);
    }
    
    public function insertSQL(string $sql, array $parameters = []): void
    {
        $this->capsule->connection()->insert($sql, $parameters);
        return;
    }
    
    /**
     * @return void
     */
    public function updateSQL(string $sql, array $parameters = []): void
    {
        $this->capsule->connection()->update($sql, $parameters);
        return;
    }
    
    /**
     * @return void
     */
    public function deleteSQL(string $sql, array $parameters = []): void
    {
        $this->capsule->connection()->delete($sql, $parameters);
        return;
    }
    
    public function findValidKarajanToken(): array
    {
        $now = date('c');
        
        return $this->capsule->connection()->select(
            "SELECT *
            FROM c4a_karajan_token
            WHERE expires_at > ?
            LIMIT 1",
            [$now]
            );
    }
    
    public function findKarajanServer(string $serverType = 'karajan'): array
    {
        return $this->capsule->connection()->select(
            "SELECT *
            FROM tblservers
            WHERE type = ?
            LIMIT 1",
            [$serverType]
            );
    }
    
    public function getRawProducts(): array
    {
        return $this->capsule->connection()->select(
            'SELECT * FROM tblproducts'
            );
    }
    
    public function getRawProduct(int $productId): array
    {
        return $this->capsule->connection()->select(
            'SELECT * FROM tblproducts WHERE id = ?',
            [$productId]
            );
    }
    
    public function countClientProductsByServerType(int $clientId, string $serverType): int
    {
        $results = $this->capsule->connection()->select(
            "SELECT COUNT(DISTINCT hosting.id) as count
             FROM tblhosting AS hosting
             JOIN tblproducts AS product
             ON product.id = hosting.packageid
             WHERE product.servertype = ? AND hosting.userid = ?",
            [$serverType, $clientId]
            );
        
        return (int) ($results[0]->count ?? 0);
    }
    
    public function getClientProductsByServerType(int $clientId, string $serverType): array
    {
        return $this->capsule->connection()->select(
            "SELECT hosting.id, hosting.domain, hosting.domainstatus as status, product.name
             FROM tblhosting AS hosting
             JOIN tblproducts AS product
             ON product.id = hosting.packageid
             WHERE product.servertype = ? AND hosting.userid = ?",
            [$serverType, $clientId]
            );
    }
    
    public function getClientProductByServerType(int $clientId, int $productId, string $serverType): array
    {
        return $this->capsule->connection()->select(
            "SELECT hosting.id, hosting.domain, hosting.domainstatus as status, product.name
             FROM tblhosting AS hosting
             JOIN tblproducts AS product
             ON product.id = hosting.packageid
             WHERE product.servertype = ? AND hosting.userid = ? AND hosting.id = ?",
            [$serverType, $clientId, $productId]
            );
    }
    
    public function countClientProductsBySlug(int $clientId, string $slug): int
    {
        $results = $this->capsule->connection()->select(
            "SELECT COUNT(DISTINCT hosting.id) as count
             FROM tblhosting AS hosting
             JOIN tblproducts AS product
             ON product.id = hosting.packageid
             JOIN tblservers AS server
             ON server.type = product.servertype
             WHERE product.slug = ? AND hosting.userid = ?",
            [$slug, $clientId]
            );
        
        return (int) ($results[0]->count ?? 0);
    }
    
    public function getClientProductsBySlug(int $clientId, string $slug): array
    {
        return $this->capsule->connection()->select(
            "SELECT hosting.id as id,
                    hosting.created_at as created_at,
                    hosting.regdate as regdate,
                    hosting.domain as domain,
                    hosting.domainstatus as status,
                    product.name as name,
                    customfieldvalue.value as internal_status,
                    productgroup.name as productgroup
             FROM tblhosting AS hosting
             JOIN tblproducts AS product
                ON product.id = hosting.packageid
             JOIN tblproductgroups AS productgroup
                ON product.gid = productgroup.id
             JOIN tblservers AS server
                ON server.type = product.servertype
             JOIN tblcustomfieldsvalues AS customfieldvalue
                ON customfieldvalue.relid = hosting.id
             JOIN tblcustomfields AS customfield
                ON customfield.id = customfieldvalue.fieldid
             WHERE product.slug = ?
               AND hosting.userid = ?
               AND customfield.fieldname = ?",
            [$slug, $clientId, 'internalStatus']
            );
    }
    
    public function getClientProductBySlug(int $clientId, int $hostingId, string $slug): array
    {
        return $this->capsule->connection()->select(
            "SELECT hosting.id as id,
                    hosting.created_at as created_at,
                    hosting.regdate as regdate,
                    hosting.username as username,
                    hosting.password as password,
                    hosting.domain as domain,
                    hosting.domainstatus as status,
                    customfieldvalue.value as internal_status,
                    product.name as name,
                    server.username as serverusername,
                    server.password as serverpassword,
                    server.hostname as serverhostname,
                    server.port as serverport,
                    server.accesshash as serveraccesshash,
                    productgroup.name as productgroup
             FROM tblhosting AS hosting
             JOIN tblproducts AS product
                ON product.id = hosting.packageid
             JOIN tblproductgroups AS productgroup
                ON product.gid = productgroup.id
             JOIN tblservers AS server
                ON server.id = hosting.server
             JOIN tblcustomfieldsvalues AS customfieldvalue
                ON customfieldvalue.relid = hosting.id
             JOIN tblcustomfields AS customfield
                ON customfield.id = customfieldvalue.fieldid
             WHERE product.slug = ?
               AND hosting.userid = ?
               AND hosting.id = ?
               AND customfield.fieldname = ?",
            [$slug, $clientId, $hostingId, 'internalStatus']
            );
    }
    
    public function getClientProductCustomFieldData(int $productId): array
    {
        return $this->capsule->connection()->select(
            "SELECT customfieldvalue.id,
                    customfieldvalue.value,
                    customfield.fieldname
             FROM tblhosting AS hosting
             JOIN tblcustomfieldsvalues AS customfieldvalue
                ON customfieldvalue.relid = hosting.id
             JOIN tblcustomfields AS customfield
                ON customfield.id = customfieldvalue.fieldid
             WHERE hosting.id = ?",
            [$productId]
            );
    }
    
    public function countClientProductAddons(int $clientId): int
    {
        $results = $this->capsule->connection()->select(
            "SELECT COUNT(DISTINCT hostingaddon.id) as count
             FROM tblhostingaddons AS hostingaddon
             JOIN tbladdons AS addon
                ON addon.id = hostingaddon.addonid
             WHERE hostingaddon.userid = ?",
            [$clientId]
            );
        
        return (int) ($results[0]->count ?? 0);
    }
    
    
    public function getClientProductAddons(int $clientId): array
    {
        return $this->capsule->connection()->select(
            "SELECT hostingaddon.id as id,
                    hostingaddon.hostingid as hosting_id,
                    hostingaddon.created_at as created_at,
                    hostingaddon.status as status,
                    addon.name as name,
                    customfieldvalue.value as internal_status
             FROM tblhostingaddons AS hostingaddon
             JOIN tbladdons AS addon
                ON addon.id = hostingaddon.addonid
             JOIN tblhosting AS hosting
                ON hosting.id = hostingaddon.hostingid
             JOIN tblcustomfieldsvalues AS customfieldvalue
                ON customfieldvalue.relid = hosting.id
             JOIN tblcustomfields AS customfield
                ON customfield.id = customfieldvalue.fieldid
             WHERE hosting.userid = ?
               AND customfield.fieldname = ?",
            [$clientId, 'internalStatus']
            );
    }
    
    public function getClientProductAddon(int $clientId, int $hostingAddonId): array
    {
        return $this->capsule->connection()->select(
            "SELECT hostingaddon.id as id,
                    hostingaddon.created_at as created_at,
                    hostingaddon.status as status,
                    addon.name as name,
                    customfieldvalue.value as internal_status
             FROM tblhostingaddons AS hostingaddon
             JOIN tbladdons AS addon
                ON addon.id = hostingaddon.addonid
             JOIN tblhosting AS hosting
                ON hosting.id = hostingaddon.hostingid
             JOIN tblcustomfieldsvalues AS customfieldvalue
                ON customfieldvalue.relid = hosting.id
             JOIN tblcustomfields AS customfield
                ON customfield.id = customfieldvalue.fieldid
             WHERE hostingaddon.userid = ?
               AND hostingaddon.id = ?
               AND customfield.fieldname = ?",
            [$clientId, $hostingAddonId, 'internalStatus']
            );
    }
    
    public function getClientProductAddonCustomFieldData(int $productId): array
    {
        return $this->capsule->connection()->select(
            "SELECT customfieldvalue.id,
                    customfieldvalue.value,
                    customfield.fieldname
             FROM tblhostingaddons AS hostingaddon
             JOIN tblcustomfieldsvalues AS customfieldvalue
                ON customfieldvalue.relid = hostingaddon.id
             JOIN tblcustomfields AS customfield
                ON customfield.id = customfieldvalue.fieldid
             WHERE hostingaddon.id = ?",
            [$productId]
            );
    }
    
    public function getCustomFieldValuesByType(string $type): array
    {
        return $this->capsule->connection()->select(
            "SELECT customfieldvalue.id as id,
                    customfieldvalue.value as value,
                    customfield.fieldname as field_name,
                    customfield.type as type
             FROM tblcustomfieldsvalues AS customfieldvalue
             JOIN tblcustomfields AS customfield
                ON customfield.id = customfieldvalue.fieldid
             WHERE customfield.type = ?",
            [$type]
            );
    }
    
    public function getCustomFieldValuesByTypeAndFieldName(string $type, string $fieldName): array
    {
        return $this->capsule->connection()->select(
            "SELECT customfieldvalue.id as id,
                    customfieldvalue.value as value,
                    customfield.fieldname as field_name,
                    customfield.type as type
             FROM tblcustomfieldsvalues AS customfieldvalue
             JOIN tblcustomfields AS customfield
                ON customfield.id = customfieldvalue.fieldid
             WHERE customfield.type = ?
               AND customfield.fieldname = ?",
            [$type, $fieldName]
            );
    }
    
    public function getClientProductRegion(int $productId): array
    {
        return $this->capsule->connection()->select(
            "SELECT productconfigoptionssub.optionname
             FROM tblhosting AS hosting
             JOIN tblhostingconfigoptions AS hostingconfigoption
                ON hostingconfigoption.relid = hosting.id
             JOIN tblproductconfigoptionssub AS productconfigoptionssub
                ON productconfigoptionssub.configid = hostingconfigoption.configid
               AND productconfigoptionssub.id = hostingconfigoption.optionid
             JOIN tblproductconfigoptions AS productconfigoptions
                ON productconfigoptions.id = hostingconfigoption.configid
               AND productconfigoptions.optiontype = 1
             WHERE hosting.id = ?",
            [$productId]
            );
    }
    
    public function getClientDomains(int $clientId): array
    {
        return $this->api->call('GetClientsDomains', [
            'clientid' => $clientId,
            'stats' => true,
        ]);
    }
    
    
    public function getClientDomain(int $clientId, string $domainName): ?array
    {
        $results = $this->api->call('GetClientsDomains', [
            'clientid' => $clientId,
            'stats' => true,
        ]);
        
        if (($results['totalresults'] ?? 0) > 0) {
            $domains = $results['domains']['domain'] ?? [];
            
            foreach ($domains as $domain) {
                if (($domain['domainname'] ?? '') === $domainName) {
                    return $domain;
                }
            }
        }
        
        return null;
    }
    
    
    public function getClientActiveDomains(int $clientId): array
    {
        $results = $this->api->call('GetClientsDomains', [
            'clientid' => $clientId,
            'stats' => true,
        ]);
        
        $response = [];
        
        if (($results['totalresults'] ?? 0) > 0) {
            $domains = $results['domains']['domain'] ?? [];
            
            foreach ($domains as $domain) {
                if (($domain['status'] ?? '') === 'Active') {
                    $response[] = $domain;
                }
            }
        }
        
        return $response;
    }
    
    public function getClientPendingDomains(int $clientId): array
    {
        $results = $this->api->call('GetClientsDomains', [
            'clientid' => $clientId,
            'stats' => true,
        ]);
        
        $response = [];
        
        if (($results['totalresults'] ?? 0) > 0) {
            $domains = $results['domains']['domain'] ?? [];
            
            foreach ($domains as $domain) {
                if (($domain['status'] ?? '') === 'Pending') {
                    $response[] = $domain;
                }
            }
        }
        
        return $response;
    }
    
    public function getClientExpiredDomains(int $clientId): array
    {
        $results = $this->api->call('GetClientsDomains', [
            'clientid' => $clientId,
            'stats' => true,
        ]);
        
        $response = [];
        $today = new \DateTimeImmutable();
        
        if (($results['totalresults'] ?? 0) > 0) {
            $domains = $results['domains']['domain'] ?? [];
            
            foreach ($domains as $domain) {
                $status = $domain['status'] ?? '';
                $expiryDate = $domain['expirydate'] ?? null;
                
                if (
                    $expiryDate !== null &&
                    !in_array($status, ['Active', 'Pending'], true) &&
                    new \DateTimeImmutable($expiryDate) < $today
                    ) {
                        $response[] = $domain;
                    }
            }
        }
        
        return $response;
    }
    
    public function getClientProducts(int $clientId): array
    {
        return $this->api->call('GetClientsProducts', [
            'clientid' => $clientId,
            'stats' => true,
        ]);
    }
    
    public function getClientProductById(int $clientId, int $productId): array
    {
        return $this->api->call('GetClientsProducts', [
            'clientid' => $clientId,
            'pid' => $productId,
            'stats' => true,
        ]);
    }
    
    /**
     * 
     * @param int $clientId
     * @param int $serviceId
     * @return array{
     *     result: string,
     *     totalresults: int,
     *     products?: mixed
     * }
     */
    public function getClientProductByServiceId(int $clientId, int $serviceId): array
    {
        return $this->api->call('GetClientsProducts', [
            'clientid' => $clientId,
            'serviceid' => $serviceId,
            'stats' => true,
        ]);
    }
    
    public function getClientProductByDomain(int $clientId, string $domain): ?array
    {
        $response = $this->api->call('GetClientsProducts', [
            'clientid' => $clientId,
            'domain' => $domain,
            'stats' => true,
        ]);
        
        if (($response['totalresults'] ?? 0) > 0) {
            return $response['products']['product'][0] ?? null;
        }
        
        return null;
    }
    
    public function getClientDetails(int $clientId): array
    {
        return $this->api->call('GetClientsDetails', [
            'clientid' => $clientId,
            'stats' => true,
        ]);
    }
    
    public function getDomainWhois($domain): array
    {
        return $this->api->call('DomainWhois', [
            'domain' => $domain,
        ]);
    }
    
    public function getDecryptedPassword($password): array
    {
        return $this->api->call('DecryptPassword', [
            'password2' => $password,
        ]);
    }
}
