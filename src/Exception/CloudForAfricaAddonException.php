<?php

namespace WHMCS\Cloud4Africa\Exception;

use WHMCS\Cloud4Africa\Util\Translator;

class CloudForAfricaAddonException
{
    public const FORMAT_JSON = 'json';
    public const FORMAT_HTML = 'html';

    public function __construct(string $message = null, string $format = null, $code = 500)
    {
        $translator = new Translator();

        if ($format == self::FORMAT_JSON) {
            http_response_code($code);
            echo $message ?? $translator->trans('error.default');
            exit();
        }

        throw new \Exception($message ?? $translator->trans('error.default'));
    }
}
