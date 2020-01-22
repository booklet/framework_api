<?php
class MysqlORMTime
{
    /**
    * Set created_at timestamp when create record
    * Set updated_at any time when update record
    */
    public static function setTimestamps($model_obj, $obj)
    {
        $date_time_format = Config::get('mysqltime') ?? "Y-m-d H:i:s";

        if ($model_obj->isNewRecord() and !isset($obj->created_at)) {
            $obj->created_at = date($date_time_format);
        }
        $obj->updated_at = date($date_time_format);

        return $obj;
    }
}
