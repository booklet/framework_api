<?php
class HtmlPaginate
{
    const NUM_PLACEHOLDER = '(:num)';

    private $total_items;
    private $items_per_page;
    private $current_page;
    private $url_pattern;
    private $total_pages;
    private $max_pages_to_show = 10;
    private $previous_text = 'Poprzednia';
    private $next_text = 'Następna';

    public function __construct($paginate_data)
    {
        $this->total_items = $paginate_data['total_items'];
        $this->items_per_page = $paginate_data['items_per_page'];
        $this->current_page = $paginate_data['current_page'];
        $this->url_pattern = $paginate_data['url_pattern'];

        $this->updateNumPages();
    }

    private function updateNumPages()
    {
        $this->total_pages = ($this->items_per_page == 0 ? 0 : (int) ceil($this->total_items / $this->items_per_page));
    }

    public function getCurrentPage()
    {
        return $this->current_page;
    }

    public function getPageUrl($page_num)
    {
        return str_replace(self::NUM_PLACEHOLDER, $page_num, $this->url_pattern);
    }

    public function getPrevUrl()
    {
        if (!$this->getPrevPage()) {
            return null;
        }

        return $this->getPageUrl($this->getPrevPage());
    }

    public function getPrevPage()
    {
        if ($this->current_page > 1) {
            return $this->current_page - 1;
        }

        return null;
    }

    public function getNextUrl()
    {
        if (!$this->getNextPage()) {
            return null;
        }

        return $this->getPageUrl($this->getNextPage());
    }

    public function getNextPage()
    {
        if ($this->current_page < $this->total_pages) {
            return $this->current_page + 1;
        }

        return null;
    }

    public function getPages()
    {
        $pages = [];
        if ($this->total_pages <= 1) {
            return [];
        }

        if ($this->total_pages <= $this->max_pages_to_show) {
            for ($i = 1; $i <= $this->total_pages; ++$i) {
                $pages[] = $this->createPage($i, $i == $this->current_page);
            }
        } else {
            // Determine the sliding range, centered around the current page.
            $num_adjacents = (int) floor(($this->max_pages_to_show - 3) / 2);
            if ($this->current_page + $num_adjacents > $this->total_pages) {
                $sliding_start = $this->total_pages - $this->max_pages_to_show + 2;
            } else {
                $sliding_start = $this->current_page - $num_adjacents;
            }

            if ($sliding_start < 2) {
                $sliding_start = 2;
            }
            $sliding_end = $sliding_start + $this->max_pages_to_show - 3;

            if ($sliding_end >= $this->total_pages) {
                $sliding_end = $this->total_pages - 1;
            }
            // Build the list of pages.
            $pages[] = $this->createPage(1, $this->current_page == 1);
            if ($sliding_start > 2) {
                $pages[] = $this->createPageEllipsis();
            }
            for ($i = $sliding_start; $i <= $sliding_end; ++$i) {
                $pages[] = $this->createPage($i, $i == $this->current_page);
            }
            if ($sliding_end < $this->total_pages - 1) {
                $pages[] = $this->createPageEllipsis();
            }
            $pages[] = $this->createPage($this->total_pages, $this->current_page == $this->total_pages);
        }

        return $pages;
    }

    private function createPage($page_num, $is_current = false)
    {
        return [
            'num' => $page_num,
            'url' => $this->getPageUrl($page_num),
            'is_current' => $is_current,
        ];
    }

    private function createPageEllipsis()
    {
        return [
            'num' => '...',
            'url' => null,
            'is_current' => false,
        ];
    }

    public function toHtml()
    {
        if ($this->total_pages <= 1) {
            return '';
        }

        $html = '<ul class="pagination">';
        if ($this->getPrevUrl()) {
            $html .= '<li><a href="' . $this->getPrevUrl() . '">&laquo; ' . $this->previous_text . '</a></li>';
        }
        foreach ($this->getPages() as $page) {
            if ($page['url']) {
                $html .= '<li' . ($page['is_current'] ? ' class="active"' : '') . '><a href="' . $page['url'] . '">' . $page['num'] . '</a></li>';
            } else {
                $html .= '<li class="disabled"><span>' . $page['num'] . '</span></li>';
            }
        }
        if ($this->getNextUrl()) {
            $html .= '<li><a href="' . $this->getNextUrl() . '">' . $this->next_text . ' &raquo;</a></li>';
        }
        $html .= '</ul>';

        return $html;
    }
}
