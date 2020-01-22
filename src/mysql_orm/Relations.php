<?php
class Relations
{
    public $obj;
    public $obj_class_name;
    public $fn_name;
    public $fn_args;
    public $sql_query;
    public $sql_params;

    public function __construct($obj, $fn_name, $fn_args)
    {
        $this->obj = $obj;
        $this->obj_class_name = get_class($obj);
        $this->fn_name = $fn_name;
        $this->fn_args = $fn_args;
    }

    // check if model has
    public static function isRelationMethod($obj, $fn_name)
    {
        $model = get_class($obj);

        foreach ($model::relations() as $relation_fn_name => $relation_params) {
            if ($relation_fn_name == $fn_name) {
                return true;
            }
        }

        return false;
    }

    public function getRelationsObjects(array $params = [])
    {
        foreach ($this->obj_class_name::relations() as $relation_fn_name => $relation_params) {
            if ($relation_fn_name == $this->fn_name) {
                if ($relation_params['relation'] == 'has_many') {
                    // TODO wywalic paginacje
                    if (isset($params[1]) and isset($params[1]['paginate_data'])) {
                        $where_params = [];
                        if (isset($params[0])) {
                            $where_params = $params[0];
                        }

                        list($results, $paginate_data) = $relation_params['class']::whereWithPaginate($this->sqlQuery(), $this->sqlParams(), $where_params, $params[1]);

                        return [$results, $paginate_data];
                    } else {
                        return $relation_params['class']::where($this->sqlQuery(), $this->sqlParams());
                    }
                }
                if ($relation_params['relation'] == 'belongs_to') {
                    $parent_table_name = $this->fn_name . '_id';

                    return $relation_params['class']::find($this->obj->$parent_table_name);
                }
                if ($relation_params['relation'] == 'has_and_belongs_to_many') {
                    // keys
                    $this_table_id_key = $this->getTableIdKey($this->obj_class_name);
                    $target_table_id_key = $this->getTableIdKey($relation_params['class']);

                    $habtm_table_name = $this->getHABTMTableName($relation_params);

                    // buildit query
                    $query_string = 'SELECT `' . $habtm_table_name . '`.* FROM `' . $habtm_table_name . '` WHERE `' . $this_table_id_key . '` = ' . $this->obj->id;
                    $result = mysqli_query(MyDB::db(), $query_string);

                    // collect ids
                    $relation_objects_ids = [];
                    while ($row = $result->fetch_assoc()) {
                        $relation_objects_ids[] = $row[$target_table_id_key];
                    }

                    if (!empty($relation_objects_ids)) {
                        $relation_objects_ids_str = join(',', $relation_objects_ids);

                        $where_params = [];
                        if (isset($params[0])) {
                            $where_params = $params[0];
                        }

                        if (isset($params[1]) and isset($params[1]['paginate_data'])) {
                            list($results, $paginate_data) = $relation_params['class']::whereWithPaginate('id IN (' . $relation_objects_ids_str . ')', [], $where_params, $params[1]);

                            return [$results, $paginate_data];
                        } else {
                            return $relation_params['class']::where('id IN (' . $relation_objects_ids_str . ')', [], $where_params);
                        }
                    } else {
                        if (isset($params[1]) and isset($params[1]['paginate_data'])) {
                            // return empty, we need paginate_data
                            list($results, $paginate_data) = $relation_params['class']::whereWithPaginate('id IN (0)', [], [], $params[1]);

                            return [$results, $paginate_data];
                        } else {
                            return [];
                        }
                    }
                }
            }
        }

        return false;
    }

    public function sqlQuery()
    {
        foreach ($this->obj_class_name::relations() as $relation_fn_name => $relation_params) {
            if ($relation_fn_name == $this->fn_name) {
                if ($relation_params['relation'] == 'has_many') {
                    if (isset($relation_params['foreign_key'])) {
                        return $relation_params['foreign_key'] . ' = ?';
                    } else {
                        $underscore_class_name = StringUntils::camelCaseToUnderscore($this->obj_class_name);

                        return $underscore_class_name . '_id = ?';
                    }
                }
                if ($relation_params['relation'] == 'belongs_to') {
                    return 'id = ?';
                }
            }
        }

        return false;
    }

    public function sqlParams()
    {
        foreach ($this->obj_class_name::relations() as $relation_fn_name => $relation_params) {
            if ($relation_fn_name == $this->fn_name) {
                if ($relation_params['relation'] == 'has_many') {
                    if (isset($relation_params['foreign_key'])) {
                        return [$relation_params['foreign_key'] => $this->obj->id];
                    } else {
                        $underscore_class_name = StringUntils::camelCaseToUnderscore($this->obj_class_name);

                        return [$underscore_class_name . '_id' => $this->obj->id];
                    }
                }
                if ($relation_params['relation'] == 'belongs_to') {
                    if (isset($relation_params['foreign_key'])) {
                        $parent_table_name = $relation_params['foreign_key'];

                        return ['id' => $this->obj->$parent_table_name];
                    } else {
                        $parent_table_name = $this->fn_name . '_id';

                        return ['id' => $this->obj->$parent_table_name];
                    }
                }
            }
        }

        return false;
    }

    public function sqlModel()
    {
        foreach ($this->obj_class_name::relations() as $relation_fn_name => $relation_params) {
            if ($relation_fn_name == $this->fn_name) {
                return $relation_params['class'];
            }
        }

        return false;
    }

    public function habtmPushObjects()
    {
        if (is_array($this->fn_args[0])) {
            $items = $this->fn_args[0];
        } else {
            $items = [$this->fn_args[0]];
        }

        // $this->fn_name = categoriesPush
        foreach ($this->obj_class_name::relations() as $relation_fn_name => $relation_params) {
            if ($relation_fn_name == str_replace('Push', '', $this->fn_name)) {
                $model_relation_params = $relation_params;
            }
        }
        if (!isset($model_relation_params)) {
            throw new Exception('Not found relation');
        }

        // check if passed items type is allowed
        foreach ($items as $item) {
            if ($model_relation_params['class'] != get_class($item)) {
                throw new Exception('Wrong push object class, expect ' . $model_relation_params['class'] . ' got ' . get_class($item));
            }
        }

        // keys
        $this_table_id_key = $this->getTableIdKey($this->obj_class_name);
        $target_table_id_key = $this->getTableIdKey($model_relation_params['class']);

        $habtm_table_name = $this->getHABTMTableName($model_relation_params);

        // wrong object type create wrong mysql query

        foreach ($items as $item) {
            // check if exist
            // buildit query
            $query_string = 'SELECT `' . $habtm_table_name . '`.* FROM `' . $habtm_table_name . '` WHERE `' . $this_table_id_key . '` = ' . $this->obj->id . ' AND `' . $target_table_id_key . '` = ' . $item->id;
            $result = mysqli_query(MyDB::db(), $query_string);

            if ($result->num_rows > 0) {
                // record exist, not create next
            } else {
                // create database
                $query_string = 'INSERT INTO `' . $habtm_table_name . '` (`' . $this_table_id_key . '`, `' . $target_table_id_key . '`) VALUES (' . $this->obj->id . ', ' . $item->id . ')';
                $result = mysqli_query(MyDB::db(), $query_string);
            }
        }
    }

    public function habtmDeleteObjects()
    {
        if (is_array($this->fn_args[0])) {
            $items = $this->fn_args[0];
        } else {
            $items = [$this->fn_args[0]];
        }

        // $this->fn_name = categoriesPush
        foreach ($this->obj_class_name::relations() as $relation_fn_name => $relation_params) {
            if ($relation_fn_name == str_replace('Delete', '', $this->fn_name)) {
                $model_relation_params = $relation_params;
            }
        }

        if (!isset($model_relation_params)) {
            throw new Exception('Not found relation');
        }

        // check if passed items type is allowed
        foreach ($items as $item) {
            if ($model_relation_params['class'] != get_class($item)) {
                throw new Exception('Wrong delete object class, expect ' . $model_relation_params['class'] . ' got ' . get_class($item));
            }
        }

        // keys
        $this_table_id_key = $this->getTableIdKey($this->obj_class_name);
        $target_table_id_key = $this->getTableIdKey($model_relation_params['class']);

        $habtm_table_name = $this->getHABTMTableName($model_relation_params);

        foreach ($items as $item) {
            // check if exist
            // buildit query
            $query_string = 'SELECT `' . $habtm_table_name . '`.* FROM `' . $habtm_table_name . '` WHERE `' . $this_table_id_key . '` = ' . $this->obj->id . ' AND `' . $target_table_id_key . '` = ' . $item->id;
            $result = mysqli_query(MyDB::db(), $query_string);

            if ($result->num_rows > 0) {
                // delete relations
                $query_string = 'DELETE `' . $habtm_table_name . '`.* FROM `' . $habtm_table_name . '` WHERE `' . $this_table_id_key . '` = ' . $this->obj->id . ' AND `' . $target_table_id_key . '` = ' . $item->id;
                $result = mysqli_query(MyDB::db(), $query_string);
            } else {
                // do nothing
            }
        }
    }

    private function getTableIdKey($class_name)
    {
        $class_reflex = new ReflectionClass($class_name);
        $class_name = $class_reflex->getShortName();

        return StringUntils::camelCaseToUnderscore($class_name) . '_id';
    }

    private function getHABTMTableName($relation_params)
    {
        $habtm_model_name_arr = [];
        $habtm_model_name_arr[] = StringUntils::camelCaseToUnderscore($this->obj->pluralizeClassName());
        $habtm_model_name_arr[] = StringUntils::camelCaseToUnderscore((new $relation_params['class']())->pluralizeClassName());
        sort($habtm_model_name_arr);

        return join('_', $habtm_model_name_arr);
    }
}
