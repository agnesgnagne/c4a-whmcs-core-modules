<?php

namespace WHMCS\Cloud4Africa\Service;

use WHMCS\Database\Capsule;

class Helper
{
    public static function extractRemoteServiceIdByHosting(array $hosting): ?string
    {
        $serviceIdField = Capsule::table('tblcustomfields')->where('fieldname', 'serviceId')->where('relid', $hosting['packageid'])->get();
        $serviceIdFieldValue = Capsule::table('tblcustomfieldsvalues')
            ->where('fieldid', $serviceIdField[0]->id)
            ->where('relid', $hosting['id'])
            ->get()
        ;

        return $serviceIdFieldValue[0]->value;
    }

    public static function extractRemoteUserIdByClientId(string $clientId): ?string
    {
        $remoteUserIdField = Capsule::table('tblcustomfields')->where('fieldname', 'remoteUserId')->where('type', 'client')->get();

        if (false === empty($remoteUserIdField)) {
            $remoteUserIdFieldValue = Capsule::table('tblcustomfieldsvalues')->where('fieldid', $remoteUserIdField[0]->id)->where('relid', $clientId)->get();
            return $remoteUserIdFieldValue[0]->value;
        }

        return null;
    }
}