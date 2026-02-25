<?php

namespace WHMCS\Cloud4Africa\Repository;

use WHMCS\Cloud4Africa\Service\WhmcsLocalApiInterface;
use Illuminate\Database\Capsule\Manager as Capsule;

class WhmcsRepository implements WhmcsRepositoryInterface
{
    private WhmcsLocalApiInterface $api;
    
    private Capsule $capsule;
    
    public function __construct(WhmcsLocalApiInterface $api, Capsule $capsule)
    {
        $this->api = $api;
        $this->capsule = $capsule;
    }
    
    public function select(string $sql, array $parameters = []): array
    {
        return $this->capsule->connection()->select($sql, $parameters);
    }
    
    public function count(string $sql, array $parameters = []): int
    {
        $results =$this->capsule->connection()->select($sql, $parameters);
        return (int) ($results[0]->count ?? 0);
    }
    
    public function exists(string $sql, array $parameters = []): bool
    {
        $results = $this->capsule->connection()->select($sql, $parameters);
        return !empty($results);
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
}
