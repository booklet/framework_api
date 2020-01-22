<?php
class GlobalFunctionsTest extends \CustomPHPUnitTestCase
{
    public function testH()
    {
        $this->assertEquals(h(), null);
        $this->assertEquals(h('123'), '123');
        $this->assertEquals(h('<script>alert("test")</script>'), '&lt;script&gt;alert(&quot;test&quot;)&lt;/script&gt;');

        try {
            $this->assertEquals(h(['test' => 'val']), null);
        } catch (Throwable $t) {
            $this->assertContains('Argument 1 passed to h() must be of the type string, array given', $t->getMessage());
        }
    }
}
