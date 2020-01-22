<?php
trait ModelPaginateTrait
{
    public static function allWithPaginate(array $params, array $paginate_params)
    {
        $paginate = new BKTPaginate(get_called_class(), $paginate_params);

        $results = self::all($paginate->updateParamsForResults($params));
        $results_count = self::all($paginate->updateParamsForCount($params));

        return [$results, $paginate->getDataForPagination($results_count)];
    }

    public static function whereWithPaginate($query, array $fileds, array $params, array $paginate_params)
    {
        $paginate = new BKTPaginate(get_called_class(), $paginate_params);

        $results = self::where($query, $fileds, $paginate->updateParamsForResults($params));
        $results_count = self::where($query, $fileds, $paginate->updateParamsForCount($params));

        return [$results, $paginate->getDataForPagination($results_count)];
    }
}
