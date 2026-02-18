<?php

namespace WHMCS\Cloud4Africa\Service;

use WHMCS\Database\Capsule;

class LocalApiManager
{
    private $admin;

    public function __construct($admin = null)
    {
        $this->admin = $admin;
    }

    public function getRawProducts()
    {
        $sql = 'SELECT * FROM tblproducts';
        return Capsule::connection()->select($sql);
    }

    public function getRawProduct($productId)
    {
        $sql = 'SELECT * FROM tblproducts WHERE id = ' . $productId;
        return Capsule::connection()->select($sql);
    }

    public function countClientProductsByServerType($clientId, $serverType)
    {
        $sql = "SELECT COUNT(DISTINCT(hosting.id)) as count
                FROM tblhosting AS hosting
                JOIN tblproducts AS product
                ON product.id = hosting.packageid
                WHERE product.servertype = '" . $serverType . "' AND hosting.userid = '" . $clientId . "'"
        ;
        $results = Capsule::connection()->select($sql);

        return $results[0]->count;
    }

    public function getClientProductsByServerType($clientId, $serverType)
    {
        $sql = "SELECT hosting.id as id, hosting.domain as domain, hosting.domainstatus as status, product.name as name
                FROM tblhosting AS hosting
                JOIN tblproducts AS product
                ON product.id = hosting.packageid
                WHERE product.servertype = '" . $serverType . "' AND hosting.userid = '" . $clientId . "'"
        ;
        return Capsule::connection()->select($sql);
    }

    public function getClientProductByServerType($clientId, $productId, $serverType)
    {
        $sql = "SELECT hosting.id as id, hosting.domain as domain, hosting.domainstatus as status, product.name as name
                FROM tblhosting AS hosting
                JOIN tblproducts AS product
                ON product.id = hosting.packageid
                WHERE product.servertype = '" . $serverType . "' AND hosting.userid = '" . $clientId . "' AND hosting.id = '" . $productId . "'"
        ;

        return Capsule::connection()->select($sql);
    }

    public function countClientProductsBySlug($clientId, $slug)
    {
        $sql = "SELECT COUNT(DISTINCT(hosting.id)) as count
                FROM tblhosting AS hosting
                JOIN tblproducts AS product
                ON product.id = hosting.packageid
                JOIN tblservers AS server
                ON server.type = product.servertype
                WHERE product.slug = '" . $slug . "' AND hosting.userid = '" . $clientId . "'"
        ;

        $results = Capsule::connection()->select($sql);

        return $results[0]->count;
    }

    public function getClientProductsBySlug($clientId, $slug)
    {
        $sql = "SELECT hosting.id as id, hosting.created_at as created_at, hosting.regdate as regdate, hosting.domain as domain, hosting.domainstatus as status, product.name as name, customfieldvalue.value as internal_status, productgroup.name as productgroup
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
                WHERE product.slug = '" . $slug . "' AND hosting.userid = '" . $clientId . "' AND customfield.fieldname = 'internalStatus'"
        ;

        return Capsule::connection()->select($sql);
    }

    public function getClientProductBySlug($clientId, $hostingId, $slug)
    {
        $sql = "SELECT hosting.id as id, hosting.created_at as created_at, hosting.regdate as regdate, hosting.username as username, hosting.password as password, hosting.domain as domain, hosting.domainstatus as status, customfieldvalue.value as internal_status, product.name as name, server.username as serverusername, server.password as serverpassword, server.hostname as serverhostname, server.port as serverport, server.accesshash as serveraccesshash, productgroup.name as productgroup
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
                WHERE product.slug = '" . $slug . "' AND hosting.userid = '" . $clientId . "' AND hosting.id = '" . $hostingId . "' AND customfield.fieldname = 'internalStatus'"
        ;

        return Capsule::connection()->select($sql);
    }

    /**
     * Get client product custom field data
     *
     * @param int $productId
     *
     * @return \stdClass
     */
    public function getClientProductCustomFieldData($productId)
    {
        $sql = 'SELECT customfieldvalue.id, customfieldvalue.value, customfield.fieldname
                FROM tblhosting AS hosting
                JOIN tblcustomfieldsvalues AS customfieldvalue
                ON customfieldvalue.relid = hosting.id
                JOIN tblcustomfields AS customfield
                ON customfield.id = customfieldvalue.fieldid
                WHERE hosting.id = ' . $productId
        ;

        return Capsule::connection()->select($sql);
    }

    /**
     * Count client product addons
     *
     * @param int $clientId
     * @return int
     */
    public function countClientProductAddons(int $clientId)
    {
        $sql = "SELECT COUNT(DISTINCT(hosting.id)) as count
                FROM tblhostingaddons AS hostingaddon
                JOIN tbladdons AS addon
                ON addon.id = hostingaddon.addonid
                WHERE hostingaddon.userid = '" . $clientId . "'"
        ;

        $results = Capsule::connection()->select($sql);

        return $results[0]->count;
    }

    /**
     * Get client product addons
     *
     * @param int $clientId
     * @param string $slug
     *
     * @return \stdClass
     */
    public function getClientProductAddons(int $clientId)
    {
        $sql = "SELECT hostingaddon.id as id, hostingaddon.hostingid as hosting_id, hostingaddon.created_at as created_at, hostingaddon.status as status, addon.name as name, customfieldvalue.value as internal_status
                FROM tblhostingaddons AS hostingaddon
                JOIN tbladdons AS addon
                ON addon.id = hostingaddon.addonid
                JOIN tblhosting AS hosting
                ON hosting.id = hostingaddon.hostingid
                JOIN tblcustomfieldsvalues AS customfieldvalue
                ON customfieldvalue.relid = hosting.id
                JOIN tblcustomfields AS customfield
                ON customfield.id = customfieldvalue.fieldid
                WHERE hosting.userid = '" . $clientId . "' AND customfield.fieldname = 'internalStatus'"
        ;

        return Capsule::connection()->select($sql);
    }

    /**
     * Get client product addon
     *
     * @param int $clientId
     * @param int $hostingAddonId
     * @param string $slug
     *
     * @return \stdClass
     */
    public function getClientProductAddon($clientId, $hostingAddonId, $slug)
    {
        $sql = "SELECT hostingaddon.id as id, hostingaddon.created_at as created_at, hostingaddon.status as status, addon.name as name, customfieldvalue.value as internal_status
                FROM tblhostingaddons AS hostingaddon
                JOIN tbladdons AS addon
                ON addon.id = hostingaddon.addonid
                JOIN tblhosting AS hosting
                ON hosting.id = hostingaddon.hostingid
                JOIN tblcustomfieldsvalues AS customfieldvalue
                ON customfieldvalue.relid = hosting.id
                JOIN tblcustomfields AS customfield
                ON customfield.id = customfieldvalue.fieldid
                WHERE hostingaddon.userid = '" . $clientId . "' AND hostingaddon.id = '" . $hostingAddonId . "' AND customfield.fieldname = 'internalStatus'"
        ;

        return Capsule::connection()->select($sql);
    }

    /**
     * Get client product custom field data
     *
     * @param int $productId
     *
     * @return \stdClass
     */
    public function getClientProductAddonCustomFieldData($productId)
    {
        $sql = 'SELECT customfieldvalue.id, customfieldvalue.value, customfield.fieldname
                FROM tblhostingaddons AS hostingaddon
                JOIN tblcustomfieldsvalues AS customfieldvalue
                ON customfieldvalue.relid = hostingaddon.id
                JOIN tblcustomfields AS customfield
                ON customfield.id = customfieldvalue.fieldid
                WHERE hostingaddon.id = ' . $productId
        ;

        return Capsule::connection()->select($sql);
    }

    /**
     * Get custom field values by type
     *
     * @param int $productId
     *
     * @return \stdClass
     */
    public function getCustomFieldValuesByType($type)
    {
        $sql = "SELECT customfieldvalue.id as id, customfieldvalue.value as value, customfield.fieldname as field_name, customfield.type as type
                FROM tblcustomfieldsvalues AS customfieldvalue
                JOIN tblcustomfields AS customfield
                ON customfield.id = customfieldvalue.fieldid
                WHERE customfield.type = '" . $type . "'"
        ;

        return Capsule::connection()->select($sql);
    }

    /**
     * Get custom field values by type and by fieldname
     *
     * @param int $productId
     *
     * @return \stdClass
     */
    public function getCustomFieldValuesByTypeAndFieldName($type, $fieldName)
    {
        $sql = "SELECT customfieldvalue.id as id, customfieldvalue.value as value, customfield.fieldname as field_name, customfield.type as type
                FROM tblcustomfieldsvalues AS customfieldvalue
                JOIN tblcustomfields AS customfield
                ON customfield.id = customfieldvalue.fieldid
                WHERE customfield.type = '" . $type . "' AND customfield.fieldname = '" . $fieldName . "'"
        ;

        return Capsule::connection()->select($sql);
    }

    /**
     * Get client product region
     *
     * @param int $productId
     *
     * @return \stdClass
     */
    public function getClientProductRegion(int $productId)
    {
        $sql = 'SELECT productconfigoptionssub.optionname
                FROM tblhosting AS hosting
                JOIN tblhostingconfigoptions AS hostingconfigoption
                ON hostingconfigoption.relid = hosting.id
                JOIN tblproductconfigoptionssub AS productconfigoptionssub
                ON productconfigoptionssub.configid = hostingconfigoption.configid AND productconfigoptionssub.id = hostingconfigoption.optionid
                JOIN tblproductconfigoptions AS productconfigoptions
                ON productconfigoptions.id = hostingconfigoption.configid AND productconfigoptions.optiontype = 1
                WHERE hosting.id = ' . $productId
        ;

        return Capsule::connection()->select($sql);
    }

    public function getClientDomains($clientId)
    {
        $command = 'GetClientsDomains';
        $postData = [
            'clientid' => $clientId,
            'stats' => true,
        ];

        return localAPI($command, $postData, $this->admin);
    }

    public function getClientDomain($clientId, $domainName)
    {
        $command = 'GetClientsDomains';
        $postData = [
            'clientid' => $clientId,
            'stats' => true,
        ];
        $response = null;
        $results = localAPI($command, $postData, $this->admin);

        if (false === empty($results['totalresults']) && 0 < $results['totalresults']) {
            $domains = $results['domains']['domain'];
            foreach ($domains as $domain) {
                if ($domain['domainname'] == $domainName) {
                    $response = $domain;
                }
            }
        }

        return $response;
    }

    public function getClientActiveDomains($clientId)
    {
        $command = 'GetClientsDomains';
        $postData = [
            'clientid' => $clientId,
            'stats' => true,
        ];

        $results = localAPI($command, $postData, $this->admin);
        if (false === empty($results['totalresults']) && 0 < $results['totalresults']) {
            $domains = $results['domains']['domain'];
            foreach ($domains as $domain) {
                if ($domain['status'] == 'Active') {
                    $response[] = $domain;
                }
            }
        }

        return $response;
    }

    public function getClientPendingDomains($clientId)
    {
        $command = 'GetClientsDomains';
        $postData = [
            'clientid' => $clientId,
            'stats' => true,
        ];

        $results = localAPI($command, $postData, $this->admin);
        if (false === empty($results['totalresults']) && 0 < $results['totalresults']) {
            $domains = $results['domains']['domain'];
            foreach ($domains as $domain) {
                if ($domain['status'] == 'Pending') {
                    $response[] = $domain;
                }
            }
        }

        return $response;
    }

    public function getClientExpiredDomains($clientId)
    {
        $command = 'GetClientsDomains';
        $postData = [
            'clientid' => $clientId,
            'stats' => true,
        ];

        $results = localAPI($command, $postData, $this->admin);
        if (false === empty($results['totalresults']) && 0 < $results['totalresults']) {
            $domains = $results['domains']['domain'];
            $date = new \Datetime();

            foreach ($domains as $domain) {
                if ($domain['status'] != 'Active' && $domain['status'] != 'Pending' && $domain['expirydate'] < $date->format('Y-m-d')) {
                    $response[] = $domain;
                }
            }
        }

        return $response;
    }

    public function getClientProducts($clientId)
    {
        $command = 'GetClientsProducts';
        $postData = [
            'clientid' => $clientId,
            'stats' => true,
        ];

        return localAPI($command, $postData, $this->admin);
    }

    public function getClientProductById($clientId, $productId)
    {
        $command = 'GetClientsProducts';
        $postData = [
            'clientid' => $clientId,
            'pid' => $productId,
            'stats' => true,
        ];

        return localAPI($command, $postData, $this->admin);
    }

    public function getClientProductByServiceId($clientId, $serviceId)
    {
        $command = 'GetClientsProducts';
        $postData = [
            'clientid' => $clientId,
            'serviceid' => $serviceId,
            'stats' => true,
        ];

        return localAPI($command, $postData, $this->admin);
    }

    public function getClientProductByDomain($clientId, $domain)
    {
        $command = 'GetClientsProducts';
        $postData = [
            'clientid' => $clientId,
            'domain' => $domain,
            'stats' => true,
        ];

        $response = localAPI($command, $postData, $this->admin);

        if ($response['totalresults'] > 0) {
            return $response['products']['product'][0];
        }

        return null;
    }

    public function getClientDetails($clientId)
    {
        $command = 'GetClientsDetails';
        $postData = [
            'clientid' => $clientId,
            'stats' => true,
        ];

        return localAPI($command, $postData, $this->admin);
    }

    public function getDomainWhois($domain)
    {
        $command = 'DomainWhois';
        $postData = [
            'domain' => $domain,
        ];

        return localAPI($command, $postData, $this->admin);
    }
}
