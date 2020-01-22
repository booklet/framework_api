<?php
class Request
{
    /**
    * Get all params
    */
    public static function params($route_params)
    {
        $put = Request::getPutData();
        $json = Request::getJsonData();

        return array_merge($route_params, $_POST, $put, $json, $_GET);
    }

    /**
    * Parse PUT data from php://input
    */
    public static function getPutData()
    {
        $arr_put = [];
        if ($_SERVER['REQUEST_METHOD'] != 'PUT') {
            return $arr_put;
        }

        $put_data = file_get_contents('php://input');
        if (JsonUntils::isJSON($put_data)) {
            $arr_put = ObjectUntils::objToArray(json_decode($put_data));
        } else {
            parse_str($put_data, $arr_put);
        }

        return $arr_put;
    }

    /**
    * Jesli wyslemy do aplikacji zapytanie POST z nagłowkiem  "Content-Type" rownym "application/json"
    * to dane json nie sa dostepne w tablicy post, trzeba je dekodowac.
    */
    public static function getJsonData()
    {
        $json_data = file_get_contents('php://input');
        if (JsonUntils::isJSON($json_data)) {
            return json_decode($json_data, true);
        }

        return [];
    }
}
