<?php
class BKTPaginate
{
    const PAGINATION_PER_PAGE_DEFAULT = 25;

    private $model_class;
    private $paginate_params;
    private $items_per_page;
    private $current_page;

    public function __construct($model_class, $paginate_params)
    {
        $this->model_class = $model_class;
        $this->paginate_params = $paginate_params;
        $this->items_per_page = $this->getItemsPerPage($paginate_params);
        $this->current_page = $this->getCurrentPage();
    }

    public function updateParamsForResults($params)
    {
        return array_merge($params, ['paginate' => $this->current_page, 'per_page' => $this->items_per_page]);
    }

    public function updateParamsForCount($params)
    {
        return array_merge($params, ['count' => true]);
    }

    public function getDataForPagination($results_count)
    {
        return [
            'total_pages' => intval(ceil($results_count / $this->items_per_page)),
            'current_page' => $this->current_page,
            'total_items' => $results_count,
            'items_per_page' => $this->items_per_page,
        ];
    }

    private function getItemsPerPage($paginate_params)
    {
        $model_items_per_page = defined($this->model_class . '::PAGINATION_PER_PAGE') ? $this->model_class::PAGINATION_PER_PAGE : null;
        $items_per_page = $this->paginate_params['per_page'] ?? $model_items_per_page ?? self::PAGINATION_PER_PAGE_DEFAULT;

        return intval($items_per_page);
    }

    private function getCurrentPage()
    {
        if (!empty($this->paginate_params['page'])) {
            return intval($this->paginate_params['page']);
        }

        return 1;
    }
}
