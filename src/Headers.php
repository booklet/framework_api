<?php
class Headers
{
    function __construct()
    {
        $this->headers = apache_request_headers();
    }

    public function authorizationToken()
    {
        if (isset($this->headers['AUTHORIZATION'])) { // home.pl server change case of headers keys
            return $this->headers['AUTHORIZATION'];
        } elseif (isset($this->headers['Authorization'])) {
            return $this->headers['Authorization'];
        } else {
            return null;
        }
    }

    public function isTesterTestRequest()
    {
        if (isset($this->headers['TesterTestRequestBKT']) ||
            isset($this->headers['testertestrequestbkt']) ||
            isset($this->headers['Testertestrequestbkt'])) { // After switch to traefik v2, camel case has change
            return true;
        } else {
            return false;
        }
    }
}

// HOOK
// add function for home.pl
if (!function_exists('apache_request_headers'))
{
    function apache_request_headers() {
        $arh = [];
        $rx_http = '/\AHTTP_/';
        foreach($_SERVER as $key => $val) {
            if (preg_match($rx_http, $key)) {
                $arh_key = preg_replace($rx_http, '', $key);
                $rx_matches = [];
                // do some nasty string manipulations to restore the original letter case
                // this should work in most cases
                $rx_matches = explode('_', $arh_key);
                if (count($rx_matches) > 0 and strlen($arh_key) > 2) {
                    foreach($rx_matches as $ak_key => $ak_val) $rx_matches[$ak_key] = ucfirst($ak_val);
                    $arh_key = implode('-', $rx_matches);
                }
                $arh[$arh_key] = $val;
            }
        }

        return($arh);
    }
}
