<?php
class AssetStampTest extends \CustomPHPUnitTestCase
{
    public function testGetStamp()
    {
        $asset = new AssetStamp([
            'tests/fixtures/asset_minifier/file1.js',
            'tests/fixtures/asset_minifier/file2.js',
        ]);

        $stamp = $asset->getStamp();
        $this->assertEquals(strlen($stamp), 32);
        $this->assertNotEquals($stamp, 'd41d8cd98f00b204e9800998ecf8427e'); // empty string hash
    }
}
