<?php
class ORMQueryBuilder
{
    public $model_obj;
    public $table_name;
    public $query;
    public $params = [];

    public function __construct($model_obj)
    {
        $this->model_obj = $model_obj;
        $this->table_name = $this->tableName($model_obj);
    }

    public function select($colums = ['*'])
    {
        // save cols to varible and create query in to_sql
        $this->query = 'SELECT `' . $this->table_name . '`.* FROM `' . $this->table_name . '`';
    }

    public function where($query, $params = [])
    {
        $this->query .= ' WHERE ' . $query;
        $this->params = array_merge($this->params, $params);
    }

    public function orderBy($column, $direction = 'ASC')
    {
        $this->query .= ' ORDER BY ' . $column . ' ' . $direction;
    }

    public function limit($num)
    {
        $this->query .= ' LIMIT ' . $num;
    }

    public function toSql()
    {
        // zbudowanie zapytania sql na bazie zmiennych, jakich?
        return $this->query;
    }

    public function get()
    {
        //       $sth = $dbh->prepare('SELECT name, colour, calories
//       FROM fruit
//       WHERE calories < ? AND colour = ?');
//       $sth->bindValue(1, $calories, PDO::PARAM_INT);
//       $sth->bindValue(2, $colour, PDO::PARAM_STR);
//       $sth->execute();
    }

    private function tableName($model_obj)
    {
        $class_pluralize_name = $model_obj->pluralizeClassName();

        return StringUntils::camelCaseToUnderscore($class_pluralize_name);
    }

    //  SELECT
    //    *
    //  FROM
    //    yourtable
    //  WHERE
    //    date BETWEEN '2012-01-01' AND '2012-06-01';
    //

    //  SELECT
    //      column_1, column_2, ...
    //  FROM
    //      table_1
    //  [INNER | LEFT |RIGHT] JOIN table_2 ON conditions
    //  WHERE
    //      conditions
    //  GROUP BY column_1
    //  HAVING group_conditions
    //  ORDER BY column_1
    //  LIMIT offset, length;

    //  SELECT followed by a list of comma-separated columns or an asterisk (*) to indicate that you want to return all columns.
    //  FROM specifies the table or view where you want to query the data.
    //  JOIN gets data from other tables based on certain join conditions.
    //  WHERE filters rows in the result set.
    //  GROUP BY groups a set of rows into groups and applies aggregate functions on each group.
    //  HAVING filters group based on groups defined by GROUP BY clause.
    //  ORDER BY specifies a list of columns for sorting.
    //  LIMIT constrains the number of returned rows.
}
