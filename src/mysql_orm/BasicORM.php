<?php
trait BasicORM
{
    /**
     * Get all records from database.
     *
     * @return array of objects
     */
    public static function all(array $params = [])
    {
        // na podstawie modelu wygeneruj zapytanie sql

        $orm = new MysqlORM(MyDB::db(), self::getModelInstance());
        $results = $orm->where('', [], $params);

        return $results;
    }

    /**
     * Get record from database base on id.
     *
     * @param int $id
     *
     * @return object
     */
    public static function find($id, array $params = [])
    {
        $params['limit'] = 1;

        $orm = new MysqlORM(MyDB::db(), self::getModelInstance());
        $results = $orm->where('id = ?', ['id' => $id], $params);

        if (empty($results)) {
            throw new Exception('Couldn\'t find ' . get_called_class() . ' with id=' . $id, 404);
        } else {
            return $results[0]; // return single object, not array of one object
        }
    }

    /**
     * Get record by field.
     *
     * @param string $attribute
     * @param string $value
     *
     * @return array of objects
     */
    public static function findBy($attribute, $value, array $params = [])
    {
        $params['limit'] = 1;

        $orm = new MysqlORM(MyDB::db(), self::getModelInstance());
        $results = $orm->where($attribute . ' = ?', [$attribute => $value], $params);

        if (empty($results)) {
            return null;
        } else {
            return $results[0]; // return single object, not array of one object
        }
    }

    /**
     * Get record by field.
     *
     * @param string $query
     * @param array  $fileds
     *
     * @return array of objects
     */
    public static function where($query, array $fileds = [], array $params = [])
    {
        $orm = new MysqlORM(MyDB::db(), self::getModelInstance());
        $results = $orm->where($query, $fileds, $params);

        return $results;
    }

    /**
     * Get first record.
     *
     * @param array $params
     *
     * @return object
     */
    public static function first(array $params = [])
    {
        $params['limit'] = 1;

        $orm = new MysqlORM(MyDB::db(), self::getModelInstance());
        $results = $orm->where('', [], $params);

        if (empty($results)) {
            return null;
        } else {
            return $results[0]; // return single object, not array of one object
        }
    }

    /**
     * Get last record.
     *
     * @param array $params
     *
     * @return object
     */
    public static function last(array $params = [])
    {
        $params['limit'] = 1;
        $params['order'] = 'id DESC';

        $orm = new MysqlORM(MyDB::db(), self::getModelInstance());
        $results = $orm->where('', [], $params);

        if (empty($results)) {
            return null;
        } else {
            return $results[0]; // return single object, not array of one object
        }
    }

    /**
     * Qustom sql query.
     *
     * @param array $params
     *
     * @return object
     */
    public static function sql($query, array $fileds, array $params = [])
    {
        $extra_params = self::extra_params($params);
        self::$class = get_called_class();
        $class_pluralize_name = (new self::$class())->pluralizeClassName();
        self::$table = StringUntils::camelCaseToUnderscore($class_pluralize_name);

        self::$query = MyDB::db()->prepare($sql);
        self::bindParams($fileds);
        $objects = self::run_query_get_results_objects();

        return $objects;
    }

    /**
     * Save model in database.
     */
    public function save(array $params = [])
    {
        $orm = new MysqlORM(MyDB::db(), $this);

        return $orm->save($params);
    }

    /**
     * Update database record.
     */
    public function update(array $attributes, array $params = [])
    {
        // actualize object with new params
        foreach ($attributes as $key => $value) {
            $this->$key = $value;
            // $this->assignAttribute($key, $value);
        }

        return $this->save($params);
    }

    /**
     * Destroy database record.
     */
    public function destroy()
    {
        $orm = new MysqlORM(MyDB::db(), $this);

        return $orm->destroy();
    }

    /**
     * Create.
     */
    public static function create(array $attributes)
    {
        $object = self::getModelInstance();

        foreach ($attributes as $key => $value) {
            $object->$key = $value;
        }

        return $object->save();
    }

    /**
     * Get current model instance.
     */
    private static function getModelInstance()
    {
        $model_class = get_called_class();

        return new $model_class();
    }
}
