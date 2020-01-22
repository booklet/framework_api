<?php
class MysqlORM
{
    private $db_connection;
    public $model_obj;
    private $model_class_name;
    private $table_name;

    public function __construct($db_connection, $model_obj)
    {
        $this->db_connection = $db_connection;
        $this->model_obj = $model_obj;
        $this->model_class_name = get_class($model_obj);
        $this->table_name = $this->tableName($model_obj);
    }

    /**
     * Buildit query statement and return objects.
     */
    public function where($query, array $fileds = [], array $params = [])
    {
        $query_string = MysqlORMQueryString::where($this->table_name, $query, $params);
        $query_string .= MysqlORMExtraParams::extraParams($params);

        $query_statement = $this->prepareStatement($query_string);
        $query_statement = MysqlORMBinder::bindQueryParams($query_statement, $this->model_obj, $fileds);

        return $this->runQueryGetResultsObjects($query_statement, $params);
    }

    /**
     * Save/update model in database.
     */
    public function save(array $params = [])
    {
        // Exit if not valid model
        $validate = $params['validate'] ?? true;
        if ($validate and !$this->model_obj->isValid()) {
            return false;
        }

        $db_obj = MysqlORMObjectCreator::createDbObject($this->model_obj);
        $db_obj = MysqlORMTime::setTimestamps($this->model_obj, $db_obj);

        // Buildit query
        if ($this->model_obj->isNewRecord()) {
            $query_statement = $this->builditNewRecordQuery($db_obj);
        } else {
            $query_statement = $this->builditUpdateRecordQuery($db_obj);
        }

        // Callback before save
        $callbacks = $params['callbacks'] ?? true;
        if ($callbacks and method_exists($this->model_obj, 'beforeSave')) {
            $this->model_obj->beforeSave();
        }

        $query_statement = MysqlORMBinder::bindQueryParams($query_statement, $this->model_obj, $db_obj);

        if (!$query_statement->execute()) {
            throw new Exception('Error with execute query ' . $query_statement->error);
        } else {
            // When create new record, update object id and time stamp.
            if ($this->model_obj->isNewRecord()) {
                $this->model_obj->id = $query_statement->insert_id;
                $this->model_obj->created_at = $db_obj->created_at;

                if (!isset($this->model_obj->oryginal_record)) {
                    // add this to solve use update() after save(), witout get form database
                    $this->model_obj->oryginal_record = [];

                    foreach ((new $this->model_class_name())->fields() as $key => $value) {
                        $this->model_obj->oryginal_record[$key] = $this->model_obj->$key;
                    }

                    unset($this->model_obj->oryginal_record['id']);
                    unset($this->model_obj->oryginal_record['created_at']);
                    unset($this->model_obj->oryginal_record['updated_at']);
                }
            }
            $this->model_obj->updated_at = $db_obj->updated_at;

            // check if nested attributes
            $this->saveNestedObjects();

            // save habtm ids
            $this->saveHABTMIdsObjects();

            // callback after save
            if ($callbacks and method_exists($this->model_obj, 'afterSave')) {
                $this->model_obj->afterSave();
            }

            return true;
        }
    }

    private function runQueryGetResultsObjects($query_statement, $params = [])
    {
        $query_statement->execute();
        $result = $query_statement->get_result();

        if (isset($params['count']) and $params['count'] == true) {
            $res = $result->fetch_assoc();
            $response = $res['count']; // return count items
        } elseif (!empty($params['sum'])) {
            $res = $result->fetch_assoc();
            $response = $res['sum'] ?? 0; // return sum items
        } else {
            $response = MysqlORMObjectCreator::createObjects($result, $this->model_class_name);
        }

        $query_statement->free_result();

        return $response;
    }

    /**
     * Create and execute delete query.
     */
    public function destroy()
    {
        if (!isset($this->model_obj->id)) {
            throw new Exception('The object was not saved in the database, so you can not delete it.');
        }

        $query = $this->prepareStatement('DELETE FROM `' . $this->table_name . '` WHERE `id` = ?');
        $query->bind_param('i', $this->model_obj->id);

        // callback before destroy
        if (method_exists($this->model_obj, 'beforeDestroy')) {
            $this->model_obj->beforeDestroy();
        }

        $is_destroy = $query->execute() ? true : false;

        // callback after destroy
        if ($is_destroy and method_exists($this->model_obj, 'afterDestroy')) {
            $this->model_obj->afterDestroy();
        }

        return $is_destroy;
    }

    /**
     * Get model database table name.
     */
    private function tableName($model_obj)
    {
        $class_pluralize_name = $model_obj->pluralizeClassName();

        return StringUntils::camelCaseToUnderscore($class_pluralize_name);
    }

    /**
     * Create sql query for new record.
     */
    private function builditNewRecordQuery($db_obj)
    {
        // obj => "`name`, `name_search`, `created_at`, `updated_at`"
        $parameters = ObjectUntils::mysqlParameters($db_obj);
        // obj => "?, ?, ?, ..."
        $parameters_values_placeholder = ObjectUntils::mysqlParametersValuesPlaceholder($db_obj);

        $query = $this->prepareStatement('INSERT INTO `' . $this->table_name . '` (' . $parameters . ') VALUES (' . $parameters_values_placeholder . ')');

        return $query;
    }

    /**
     * Create sql query for update record.
     */
    private function builditUpdateRecordQuery($db_obj)
    {
        // "UPDATE MyGuests SET lastname='Doe' WHERE id=2"
        $parameters = ObjectUntils::mysqlParametersUpdate($db_obj);
        $query = $this->prepareStatement('UPDATE `' . $this->table_name . '` SET ' . $parameters . ' WHERE `id`=?');

        return $query;
    }

    private function saveNestedObjects()
    {
        // check if object has declared nested attributes
        if (!method_exists($this->model_obj, 'acceptsNestedAtributesFor')) {
            return;
        }

        $nested_objects_params = $this->model_obj->getNestedAttributesPrams();

        // loop current object nested atributes
        foreach ($nested_objects_params as $nested_object_param) {
            foreach ($this->model_obj->{$nested_object_param['wrapper_name']} as $index => $item) {
                // if object has id, then update/delete
                if (isset($item['id'])) {
                    // find element to update
                    $nested_obj = $nested_object_param['objects_class_name']::find($item['id']);

                    // update or destroy
                    if (isset($item['_destroy']) and $item['_destroy'] == 1) {
                        $nested_obj->destroy();
                    } else {
                        $new_params = $item;
                        // dont update this params:
                        unset($new_params['id']);
                        unset($new_params['created_at']);
                        unset($new_params['updated_at']);

                        if (!$nested_obj->update($new_params)) {
                            $this->model_obj->saveErrorsInParentObject($nested_object_param['attribute_name'], $index, $nested_obj);
                        }
                    }
                } else {
                    $params = $item;

                    $class_reflex = new ReflectionClass($this->model_class_name);
                    $class_name = $class_reflex->getShortName();

                    $underscore_class_name = StringUntils::camelCaseToUnderscore($class_name);
                    $parent_key_name = $underscore_class_name . '_id';
                    $params[$parent_key_name] = $this->model_obj->id;

                    $nested_obj = new $nested_object_param['objects_class_name']($params);

                    if (!$nested_obj->save()) {
                        $this->saveErrorsInParentObject($nested_object_param['attribute_name'], $index, $nested_obj);
                    }
                }
            }

            if (!empty($this->model_obj->errors)) {
                return false;
            }

            // remove nested attributes after save
            unset($this->model_obj->{$nested_object_param['wrapper_name']});
        }
    }

    private function saveHABTMIdsObjects()
    {
        if (!method_exists($this->model_obj, 'relations')) {
            return;
        }

        $habtm_relations = MysqlORMHABTM::getAllHabtmRelationsFromModelObject($this->model_obj);

        foreach ($habtm_relations as $relation_key => $relation_params) {
            $current_ids = MysqlORMHABTM::getCurrentIdsForHabtmRelation($this->model_obj, $relation_key);
            $passed_ids = MysqlORMHABTM::getPassedIdsForHabtmRelation($this->model_obj, $relation_key);

            //  MysqlORMHABTM::addItemsToObject($this->model_obj, $relation_key, $current_ids, $passed_ids);

            // add items
            $ids_to_add = array_diff($passed_ids, $current_ids);
            foreach ($ids_to_add as $id) {
                $push_method_name = $relation_key . 'Push';

                $item = $relation_params['class']::find($id);
                $this->model_obj->$push_method_name($item);
            }

            // remove items
            $ids_to_remove = array_diff($current_ids, $passed_ids);
            foreach ($ids_to_remove as $id) {
                $delete_method_name = $relation_key . 'Delete';

                $item = $relation_params['class']::find($id);
                $this->model_obj->$delete_method_name($item);
            }
        }
    }

    private function prepareStatement($query_string)
    {
        $this->testDatabaseConnection();
        $query = $this->db_connection->prepare($query_string);
        if (!$query) {
            throw new Exception('Error with prepare database query (' . $this->db_connection->errno . ') ' . $this->db_connection->error);
        }

        return $query;
    }

    private function testDatabaseConnection()
    {
        if (isset($this->db_connection) and $this->db_connection->ping()) {
            // connection is ok
        } else {
            throw new Exception('No database connection or connection close (MysqlORM).');
        }
    }
}
