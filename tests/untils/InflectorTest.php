<?php
class InflectorTest extends \CustomPHPUnitTestCase
{
    public function testPluralize()
    {
        $word = Inflector::pluralize('Client');
        $this->assertEquals($word, 'Clients');

        $word = Inflector::pluralize('ClientCategory');
        $this->assertEquals($word, 'ClientCategories');
    }
}
