<?php
class MysqlORMExtraParamsTest extends \CustomPHPUnitTestCase
{
    public function testFnExtraParams()
    {
        $sql1 = MysqlORMExtraParams::extraParams(['limit' => 1, 'order' => 'id DESC']);
        $this->assertEquals($sql1, ' ORDER BY id DESC LIMIT 0, 1');

        $sql2 = MysqlORMExtraParams::extraParams(['limit' => 1]);
        $this->assertEquals($sql2, ' LIMIT 0, 1');

        $sql3 = MysqlORMExtraParams::extraParams(['order' => 'name DESC']);
        $this->assertEquals($sql3, ' ORDER BY name DESC');

        $sql4 = MysqlORMExtraParams::extraParams(['paginate' => 2, 'per_page' => 50]);
        $this->assertEquals($sql4, ' LIMIT 50, 50');

        $sql5 = MysqlORMExtraParams::extraParams(['paginate' => 5, 'per_page' => 25]);
        $this->assertEquals($sql5, ' LIMIT 100, 25');
    }
}
