<?php
class MysqlORMExtraParams
{
    /**
     * Generate params to sql query.
     */
    public static function extraParams(array $params)
    {
        $sql = '';

        // ORDER ['order'=>'created_at DESC']
        if (isset($params['order'])) {
            $sql .= ' ORDER BY ' . $params['order'];
        }

        // LIMIT ['limit'=>10, 'page'=>2]
        if (isset($params['limit'])) {
            $page = (int) ($params['page'] ?? 1);
            $limit = $params['limit'];
            $startpoint = ($page * $limit) - $limit;
            $sql .= ' LIMIT ' . $startpoint . ', ' . $limit;
        } elseif (isset($params['paginate'])) {
            $page = (int) ($params['paginate'] ?? 1);
            $limit = $params['per_page'] ?? BKTPaginate::PAGINATION_PER_PAGE_DEFAULT;
            $startpoint = ($page * $limit) - $limit;
            $sql .= ' LIMIT ' . $startpoint . ', ' . $limit;
        }

        return $sql;
    }
}
