<?php
class MysqlORMBinder
{
    /**
    * Create and eval bind statement command
    */
    public static function bindQueryParams($query, $model_obj, $db_obj)
    {
        // buldit bind params
        $type = [];
        $data = [];
        foreach ($db_obj as $param => $value) {
            // i  corresponding variable has type integer
            // d  corresponding variable has type double
            // s  corresponding variable has type string
            // b  corresponding variable is a blob and will be sent in packets
            $t = 's';
            if ($model_obj->fields()[$param] == 'integer') { $t = 'i'; }
            if ($model_obj->fields()[$param] == 'float') { $t = 'd'; }
            if ($model_obj->fields()[$param] == 'double') { $t = 'd'; }
            if ($model_obj->fields()[$param] == 'blob') { $t = 'b'; }
            if ($model_obj->fields()[$param] == 'text') { $t = 's'; }
            if ($model_obj->fields()[$param] == 'datetime') { $t = 's'; }
            $type[] = $t;

            if (is_array($db_obj)) {
              $data[] = '$db_obj[\'' . $param.   '\']';
            } else {
              $data[] = '$db_obj->' . $param;
            }
        }

        if (empty($data)) { return $query; }

        // if update, add id
        if (!$model_obj->isNewRecord()) {
            $type[] = 'i';
            $data[] = '$model_obj->id';
        }

        $sql_stmt = '$query->bind_param(\'' . implode('',$type) . '\', ' . implode(', ',$data) . ');'; // put bind_param line together
        eval($sql_stmt); // execute bind_param

        return $query;
    }
}
