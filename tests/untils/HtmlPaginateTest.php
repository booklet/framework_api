<?php
class HtmlPaginateTest extends \CustomPHPUnitTestCase
{
    public function testPaginateGetPages()
    {
        $paginate = new HtmlPaginate([
            'total_items' => 100,
            'items_per_page' => 30,
            'current_page' => 1,
            'url_pattern' => '/resource?page=(:num)',
        ]);

        $expect = [
            ['num' => 1, 'url' => '/resource?page=1', 'is_current' => true],
            ['num' => 2, 'url' => '/resource?page=2', 'is_current' => false],
            ['num' => 3, 'url' => '/resource?page=3', 'is_current' => false],
            ['num' => 4, 'url' => '/resource?page=4', 'is_current' => false],
        ];

        $this->assertEquals($paginate->getPages(), $expect);

        $paginate = new HtmlPaginate([
            'total_items' => 330,
            'items_per_page' => 25,
            'current_page' => 5,
            'url_pattern' => '/resource?page=(:num)',
        ]);

        $expect = [
            ['num' => 1, 'url' => '/resource?page=1', 'is_current' => false],
            ['num' => 2, 'url' => '/resource?page=2', 'is_current' => false],
            ['num' => 3, 'url' => '/resource?page=3', 'is_current' => false],
            ['num' => 4, 'url' => '/resource?page=4', 'is_current' => false],
            ['num' => 5, 'url' => '/resource?page=5', 'is_current' => true],
            ['num' => 6, 'url' => '/resource?page=6', 'is_current' => false],
            ['num' => 7, 'url' => '/resource?page=7', 'is_current' => false],
            ['num' => 8, 'url' => '/resource?page=8', 'is_current' => false],
            ['num' => 9, 'url' => '/resource?page=9', 'is_current' => false],
            ['num' => '...', 'url' => null, 'is_current' => false],
            ['num' => 14, 'url' => '/resource?page=14', 'is_current' => false],
        ];

        $this->assertEquals($paginate->getPages(), $expect);
    }

    public function testPaginateHtml()
    {
        $paginate = new HtmlPaginate([
            'total_items' => 100,
            'items_per_page' => 30,
            'current_page' => 1,
            'url_pattern' => '/resource?page=(:num)',
        ]);

        $expect = preg_replace('/\s\s+/', '', '<ul class="pagination">
            <li class="active"><a href="/resource?page=1">1</a></li>
            <li><a href="/resource?page=2">2</a></li>
            <li><a href="/resource?page=3">3</a></li>
            <li><a href="/resource?page=4">4</a></li>
            <li><a href="/resource?page=2">Następna &raquo;</a></li>
        </ul>');

        $this->assertEquals($paginate->toHtml(), $expect);

        $paginate = new HtmlPaginate([
            'total_items' => 100,
            'items_per_page' => 30,
            'current_page' => 4,
            'url_pattern' => '/resource?page=(:num)',
        ]);

        $expect = preg_replace('/\s\s+/', '', '<ul class="pagination">
            <li><a href="/resource?page=3">&laquo; Poprzednia</a></li>
            <li><a href="/resource?page=1">1</a></li>
            <li><a href="/resource?page=2">2</a></li>
            <li><a href="/resource?page=3">3</a></li>
            <li class="active"><a href="/resource?page=4">4</a></li>
        </ul>');

        $this->assertEquals($paginate->toHtml(), $expect);

        $paginate = new HtmlPaginate([
            'total_items' => 5000,
            'items_per_page' => 30,
            'current_page' => 15,
            'url_pattern' => '/resource?page=(:num)',
        ]);

        $expect = preg_replace('/\s\s+/', '', '<ul class="pagination">
            <li><a href="/resource?page=14">&laquo; Poprzednia</a></li>
            <li><a href="/resource?page=1">1</a></li>
            <li class="disabled"><span>...</span></li>
            <li><a href="/resource?page=12">12</a></li>
            <li><a href="/resource?page=13">13</a></li>
            <li><a href="/resource?page=14">14</a></li>
            <li class="active"><a href="/resource?page=15">15</a></li>
            <li><a href="/resource?page=16">16</a></li>
            <li><a href="/resource?page=17">17</a></li>
            <li><a href="/resource?page=18">18</a></li>
            <li><a href="/resource?page=19">19</a></li>
            <li class="disabled"><span>...</span></li>
            <li><a href="/resource?page=167">167</a></li>
            <li><a href="/resource?page=16">Następna &raquo;</a></li>
        </ul>');

        $this->assertEquals($paginate->toHtml(), $expect);
    }
}
