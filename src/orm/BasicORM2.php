<?php
trait BasicORM2
{
    // TODO
    // domyslne ustawienia dla zapytan w modelu - scope
    // np domyslne sortowanie dla wszystkich zapytan sql danego modelu

    // Dodac metody
    // - pluck
    // - chunk - pobieranie duzych ilosci rekorów w paczkach po x rekordow

    private static $builder = null;
    private static $instance = null;

    /**
     * Get all records from database.
     *
     * @return array of objects
     */
    public static function orm2_all(array $params = [])
    {
        self::initialize();

        // set all query
        self::$builder->select();

        return self::$instance;
    }

    public static function orm2_find($id)
    {
        self::orm2_findBy('id', $id);

        return self::$instance;
    }

    public static function orm2_findBy($field, $value)
    {
        self::initialize();

        self::orm2_where($field . ' = ?', [$value]);
        self::$builder->limit(1);

        return self::$instance;
    }

    public static function orm2_where($query, array $params = [])
    {
        self::initialize();

        self::$builder->select();
        self::$builder->where($query, $params);

        return self::$instance;
    }

    //    public static function first()
    //    {
    //
    //    }
    //
    //    public static function last()
    //    {
    //
    //    }
    //
    //    public static function count()
    //    {
    //
    //    }

    //    public static function offset()
    //    {
    //
    //    }
    //
    //    public static function limit()
    //    {
    //
    //    }
    //
    public static function orderBy($column, $direction)
    {
        self::$builder->orderBy($column, $direction);

        return self::$instance;
    }

    //    public static function insert()
    //    {
    //
    //    }
    //
    //    public static function update()
    //    {
    //
    //    }
    //
    //
    //    public static function delete()
    //    {
    //
    //    }
    //
    //    public static function touch()
    //    {
    //
    //    }

    public static function get()
    {
        return self::$builder->get();
    }

    public static function toSql()
    {
        return self::$builder->toSql();
    }

    private static function initialize()
    {
        if (self::$builder == null) {
            self::$builder = new ORMQueryBuilder(self::getModelInstance());
        }
        if (self::$instance == null) {
            self::$instance = self::$instance = self::getModelInstance();
        }
    }
}
