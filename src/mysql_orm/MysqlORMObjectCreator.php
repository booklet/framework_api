<?php
class MysqlORMObjectCreator
{
    /**
    *
    */
    public static function createObjects($result, $model_class_name)
    {
        $objects = [];

        while ($row = $result->fetch_assoc()) { // only return one row each time it is called
            $obj = new $model_class_name;

            foreach ($row as $key => $value) {
                $obj->$key = $value;
            }

            // store in oryginal_record attribute oryginal object attributes
            // to detect what attributes change and buildit update query
            // only for this who change
            $oryg = clone $obj;
            $obj->oryginal_record = [];

            foreach ($oryg->attributes() as $key => $value) {
                $obj->oryginal_record[$key] = $value;
            }

            unset($obj->oryginal_record['id']);
            unset($obj->oryginal_record['created_at']);
            unset($obj->oryginal_record['updated_at']);

            $objects[] = $obj;
        }

        return $objects;
    }

    /**
    * Create new object used to buldit query
    * Filter object attributes, leave only present and database exist params
    */
    public static function createDbObject($model_obj)
    {
        $db_obj = new stdClass();
        if ($model_obj->isNewRecord()) {
            foreach ($model_obj->fields() as $attr => $type) {
                if ($model_obj->$attr !== null) {
                    $db_obj->$attr = $model_obj->$attr;
                }
            }
        } else {
            // if object save and update, not get from databse, object dont have oryginal_record attrib
            if (isset($model_obj->oryginal_record)) {
                // Update only fields that changes
                foreach ($model_obj->oryginal_record as $attr => $val) {
                    if ($val !== $model_obj->$attr) {
                        $db_obj->{$attr} = $model_obj->$attr;
                    }
                }
            }
        }

        return $db_obj;
    }
}
